<?php
class Services_Twitter_Config
{
    /**
     * 認証情報保存ファイルのパスを保持する
     *
     * @var string ファイルパス
     */
    private $_authFile;

    /**
     * TwitterのOAuth認証に利用されるコンシューマキーを保持する
     * @var string コンシューマキー
     */
    private $_consumer_key;

    /**
     * TwitterのOAuth認証に利用されるコンシューマシークレットを保持する
     * @var string コンシューマシークレット
     */
    private $_consumer_secret;

    /**
     * 認証ページのテンプレートファイルを保持する
     * @var string 認証ページのテンプレートファイルのパス
     */
    private $_auth_page;

    /**
     * TwitterにてOAuth認証完了後にコールバックされるURIを保持する
     * @var string OAuthコールバックURI
     */
    private $_callback;

    /**
     * Services_Twitter_Configのインスタンスを作成する
     *
     */
    public function __construct() {
        $this->_authFile = realpath('./') . DIRECTORY_SEPARATOR . 'twitter.auth';
        $this->_consumer_key    = 'Rzt2HVOtG1TmcgtE8r1rQ';
        $this->_consumer_secret = 'DDHJGoElzijet5AWsNWkazAQvhVDaoxSqru20oDrdM';

        $this->_auth_page = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'auth.view.php';

        // コールバックURLの設定
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            $proc = 'https';
        } else {
            $proc = 'http';
        }
        $this->_callback = sprintf('%s://%s%s', $proc, $_SERVER['HTTP_HOST'],
                                                        htmlspecialchars($_SERVER['PHP_SELF']));

    }

    /**
     * 認証情報保存ファイルのパスを設定する
     * @param string $path 認証情報保存ファイルのパス
     */
    public function setAuthFile($path) {
        $this->_authFile = $path;
    }

    /**
     * 認証情報保存ファイルのパスを取得する
     * @return string 認証情報保存ファイルのパス
     */
    public function getAuthFile() {
    	return $this->_authFile;
    }

    /**
     * TwitterのOAuth認証に利用されるコンシューマキー、シークレットを設定する
     * @param string $key コンシューマキー
     * @param string $secret コンシューマシークレット
     */
    public function setConsumer($key, $secret) {
        $this->_consumer_key    = $key;
        $this->_consumer_secret = $secret;
    }

    /**
     * TwitterのOAuth認証に利用されるコンシューマキーを取得する
     * @return string コンシューマキー
     */
    public function getConsumerKey() {
    	return $this->_consumer_key;
    }

    /**
     * TwitterのOAuth認証に利用されるコンシューマシークレットを取得する
     * @return string コンシューマシークレット
     */
    public function getConsumerSecret() {
    	return $this->_consumer_secret;
    }

    /**
     * 認証ページのテンプレートファイルを設定する
     * @param string $path 認証ページのテンプレートファイルのパス
     */
    public function setAuthPage($path) {
    	$this->_auth_page = $path;
    }

    /**
     * 認証ページのテンプレートファイルを取得する
     * @return string 認証ページのテンプレートファイルのパス
     */
    public function getAuthPage() {
    	return $this->_auth_page;
    }

    /**
     * 認証時にコールバックされるURIを設定する
     * @param string $uri コールバックされるURI
     */
    public function setCallback($uri) {
        $this->_callback = $uri;
    }

    /**
     * 認証時にコールバックされるURIを取得する
     * @return string コールバックされるURI
     */
    public function getCallback() {
    	return $this->_callback;
    }
}