<?php
class Services_Twitter_Config
{

	/**
	 * アプリケーション認識キーを保持する
	 * @var string アプリケーション認識キー
	 */
	private $_appKey;

	/**
	 * 認証認識キーを保存するCookieの名称を保持する
	 * @var string 認証認識キーを保存するCookieの名称
	 */
	private $_cookieName;

	/**
	 * 接続用トークンを保持する
	 * @var string 接続用トークン
	 */
	private $_token;

    /**
     * 接続用秘密トークンを保持する
     * @var string 接続用秘密トークン
     */
	private $_token_secret;

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
     * トークンを読み込む関数を保持する
     * @var callback トークンを読み込む関数
     */
    private $_tokenReadFunc;

    /**
     * トークンを保存する関数を保持する
     * @var callback トークンを保存する関数
     */
    private $_tokenSaveFunc;

    /**
     * Services_Twitter_Configのインスタンスを作成する
     *
     */
    public function __construct() {
    	$this->_cookieName = 'stid';
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
     * アプリケーション認識キーを設定する
     * @param string $key アプリケーション認識キー
     */
    public function setApplicationKey($key) {
        $this->_appKey = $key;
    }

    /**
     * アプリケーション認識キーを取得する
     * @return string アプリケーション認識キー
     */
    public function getApplicationKey() {
    	return $this->_appKey;
    }

    /**
     * 認証認識キーを保存するCookieの名称を設定する
     * @param string $name 認証認識キーを保存するCookieの名称
     */
    public function setCookieName($name) {
    	$this->_cookieName = $name;
    }

    /**
     * 認証認識キーを保存するCookieの名称を取得する
     * @return string 認証認識キーを保存するCookieの名称
     */
    public function getCookieName() {
    	return $this->_cookieName;
    }

    /**
     * 接続用トークンを設定する
     * @param string $token トークン
     * @param string $tokenSecret 秘密トークン
     */
    public function setToken($token, $tokenSecret) {
    	$this->_token = $token;
    	$this->_token_secret = $tokenSecret;
    }

    /**
     * 接続用トークンを取得する
     * @return string トークン
     */
    public function getToken() {
    	return $this->_token;
    }

    /**
     * 接続用秘密トークンを取得する
     * @return string 秘密トークン
     */
    public function getTokenSecret() {
    	return $this->_token_secret;
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

    public function setTokenReadFunction($callback) {
    	$this->_tokenReadFunc = $callback;
    }

    public function setTokenSaveFunction($callback) {
    	$this->_tokenSaveFunc = $callback;
    }

    public function getAuthInfo() {
        $authInfo = array(
             'oauth_access_token' => $this->getToken()
            ,'oauth_access_token_secret' => $this->getTokenSecret()
        );

        return $authInfo;
    }

    public function raiseTokenRead() {
        $tokens = call_user_func($this->_tokenReadFunc);
        $this->setToken($tokens['oauth_access_token'], $tokens['oauth_access_token_secret']);
        return $this->getAuthInfo();
    }

    public function raiseTokenSave($tokens) {
        call_user_func($this->_tokenSaveFunc, $tokens);
    }


/*
    private $_tokenReadFunc;
    private $_tokenSaveFunc;
$config->setAuthInfoReaderFunction('readTokens');
$config->setAuthInfoRegisterFunction('saveTokens');
 */
}