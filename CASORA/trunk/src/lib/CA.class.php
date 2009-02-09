<?php

/**
 * 権限　管理者検索
 *
 * @package   
 * @author    ryun@ryun.jp
 * @version   1.0
 * @copyright Copyright(c) 2007 ryun
 */
class CA
{

    private $_openssl            = '/usr/bin/openssl';

    private $_base_dir           = './CA';

    private $_client_config_file = 'client.cnf';

    private $_ca_cert_file       = 'cacert.pem';

    private $_ca_save_dir        = '/files';

    private $_download_file      = 'cert.pfx';

    private $_common_name        = '';

    /**
     * コンストラクタ
     *
     * 
     *
     * @access  public
     * @param   string $param
     * @return  forwarding destination
     * @see     nothing
     */
    public function __construct( $param )
    {
        if( is_array($param) ) $this->setParameter( $param );
    }

    public function setParameter( array $param )
    {
        foreach( $param as $key => $val ){
            if ( ! preg_match( '/^_/', $key ) ) $key = strtolower("_$key");
            if( property_exists( get_class(), $key ) ) $this->$key = $val;
        }
    }

    public function makeCertificate( $CountryName, $stateOrProvinceName, $LocalityName, $OrganizationName, $OrganizationalUnitName, $CommonName, $EmailAddress = null, $FriendlyName = null )
    {

        $olddir = getcwd();
        chdir($this->_base_dir);

        //属性情報の設定 <= 適宜変更する
        $dn   = array('countryName'            => $CountryName, 
                      'stateOrProvinceName'    => $stateOrProvinceName,
                      'localityName'           => $LocalityName,
                      'organizationName'       => $OrganizationName,
                      'organizationalUnitName' => $OrganizationalUnitName,
                      'commonName'             => $CommonName
                     );

        //秘密鍵とCSRの作成
        $config = array('config' => $this->_client_config_file );
        $pkey = openssl_pkey_new($config);
        $csr  = openssl_csr_new($dn, $pkey, $config);

        //ファイルに保存
        //$savedir  = sprintf("%s/%d", $this->_ca_save_dir , $shop_id);
        //$pkeyfile = sprintf("%s/%d.key", $savedir, $type_id);  
        //$csrfile  = sprintf("%s/%d.csr", $savedir, $type_id);  
        $savedir  = sprintf("%s%s", $this->_base_dir, $this->_ca_save_dir);
        $pkeyfile = sprintf("%s/key", $savedir);  
        $csrfile  = sprintf("%s/csr", $savedir);  
        $crtfile  = sprintf("%s/crt", $savedir);  


        if (!is_dir($savedir)) mkdir($savedir, 0755, true); //保存ディレクトリの作成

        openssl_pkey_export_to_file($pkey, $pkeyfile);
        openssl_pkey_free($pkey);
        if (!is_file($pkeyfile) || filesize($pkeyfile) <= 0) {
            $this->setLog('err',"Fail to create private key.", 0);
            return '';
        }
        
        openssl_csr_export_to_file($csr, $csrfile);
        if (!is_file($csrfile)  || filesize($csrfile)  <= 0) {
            $this->setLog('err',"Fail to create csr.", 0);
            return '';
        }

        // 証明書にサインする
        //既に証明書が存在していたらrevokeする。
        if (is_file($crtfile) && filesize($crtfile) > 0) {
            $cmd = sprintf("%s ca -config %s -revoke %s",
                          $this->_openssl,
                          $this->_client_config_file,
                          $crtfile);
            system($cmd);
        }

        $fdspec = array(
                        0 => array('pipe', 'r'),
                        1 => array('pipe', 'w'),
                        2 => array('pipe', 'w'),
                       );

        $cmd = sprintf("%s ca -config %s -in %s -out %s",
                       $this->_openssl,
                       $this->_client_config_file,
                       $csrfile,
                       $crtfile);

        $pipes = array();
        $process = proc_open($cmd, $fdspec, $pipes);
        if (is_resource($process)) {
            fwrite($pipes[0], "y\n"); 
            fwrite($pipes[0], "y\n");
    
            fclose($pipes[0]);
            fclose($pipes[1]);
            fclose($pipes[2]);

            proc_close($process);

        } else {
            $this->setLog('err',"Fail to create ca process.");
            return '';
        }

        if (!is_file($crtfile) || filesize($crtfile) <= 0) {
            $this->setLog('err',"Fail to create crt file.");
            return '';
        }

        $FriendlyName = '"'.$FriendlyName.'"';

        // pfxファイルを作成
        $pfxfile  = sprintf("%s/pfx", $savedir);
        $cmd = sprintf("%s pkcs12 -export -in %s -inkey %s -certfile %s -out %s -name %s",
                      $this->_openssl,
                      $crtfile,
                      $pkeyfile,
                      $this->_ca_cert_file,
                      $pfxfile,
                      $FriendlyName);
        $process = proc_open($cmd, $fdspec, $pipes);
        if (is_resource($process)) {
            fwrite($pipes[0], "\n");
            fwrite($pipes[0], "\n");
    
            fclose($pipes[0]);
            fclose($pipes[1]);
            fclose($pipes[2]);
            proc_close($process);

        } else {
            $this->setLog('err','Fail to create pfx export process.');
            return '';
        }

        chdir($olddir);
    
        if ( !is_file($pfxfile) || filesize($pfxfile) == 0 ) {
            $this->setLog('err','Not found or file size is zero by pfx file.');
            return '';
        }
    
        return $pfxfile;
    }

    public function setLog( $level, $message, $code = null )
    {
        if( class_exists(Logger) ) {
            Logger::to()->$level( $message );
        }
    }
}

?>
