<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * OAuth authentication class for service provider.
 *
 * PHP versions 5
 *
 * @category  HTTP
 * @package   OAuthProvider
 * @author    Tetsuya Yoshida <tetu@eth0.jp>
 * @copyright 2010 Tetsuya Yoshida
 * @license   http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version   1.0.6
 * @link      http://openpear.org/package/HTTP_OAuthProvider
 */

/**
 * OAuth token store class for service provider.
 *
 * @category HTTP
 * @package  OAuthProvider
 * @author   Tetsuya Yoshida <tetu@eth0.jp>
 * @license  http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version  1.0.6
 * @link     http://openpear.org/package/HTTP_OAuthProvider
 */
class HTTP_OAuthProvider_Store_Memcache extends HTTP_OAuthProvider_Store
{
    protected $options = array(
        'host' => '127.0.0.1',
        'port' => 11211,
        'prefix' => 'http_oauthprovider_',
        'explain' => 3600
    );
    protected $mem = null;


    /**
     * __construct
     * 
     * Generate the HTTP_OAuthProvider_Store_Memcache instance.
     * 
     * @param Array $options Store options
     * 
     * @return HTTP_OAuthProvider_Store_Memcache
     * 
     * @throws HTTP_OAuthProvider_Store_Exception If failing in the connection.
     */
    public function __construct(array $options=array())
    {
        $this->options = array_merge($this->options, $options);
        $host = $this->options['host'];
        $port = $this->options['port'];
        // make store instance
        $this->mem = new Memcache();
        $connected = @$this->mem->connect($host, $port);
        if (!$connected) {
            $message = sprintf("Can't connect to %s:%s", $host, $port);
            throw new HTTP_OAuthProvider_Store_Exception($message, 500);
        }
    }

    /**
     * get
     * 
     * Retrieve a token
     * 
     * @param String $token A token to retrive.
     * 
     * @return Array
     */
    public function get($token)
    {
        $key = $this->options['prefix'] . $token;
        return $this->mem->get($key);
    }

    /**
     * save
     * 
     * Save a token
     * 
     * @return Boolean
     */
    public function save()
    {
        $key = $this->options['prefix'] . $this->getToken();
        return $this->mem->set($key, $this->row, $this->options['explain']);
    }

    /**
     * remove
     * 
     * Remove a token
     * 
     * @return Boolean
     */
    public function remove()
    {
        $key = $this->options['prefix'] . $this->getToken();
        return $this->mem->delete($key);
    }
}
