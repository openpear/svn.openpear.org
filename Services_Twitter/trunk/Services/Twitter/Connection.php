<?php

require_once('HTTP/OAuth/Consumer.php');

class Services_Twitter_Connection
{
	/**
	 * Twitter OAuthのRequest TokenのURI
	 * @var string Request Token URI
	 */
	private $_requestTokenUri = 'http://twitter.com/oauth/request_token';

	/**
	 * Twitter OAuthのAccess TokenのURI
	 * @var string Access Token URI
	 */
	private $_accessTokenUri = 'http://twitter.com/oauth/access_token';

	/**
	 * Twitter OAuthのAuthorizeのURI
	 * @var string Authorize URI
	 */
	private $_authorizeUri = 'http://twitter.com/oauth/authorize';

	/**
	 * 設定情報を保持する
	 * @var Services_Twitter_Config 設定情報
	 */
	private $_config;

    /**
     * 認証情報を保持する
     * @var array OAuthトークン情報
     */
    private $_authInfo;

    /**
     * HTTP_OAuthのインスタンスを保持する
     * @var object HTTP_OAuthのインスタンス
     */
    private $_oauth;

    /**
     * Services_Twitterの通信クラスをインスタンス化する
     * @param Services_Twitter_Config $config Services_Twitter設定クラス
     */
    public function __construct($config) {
        $this->_config = $config;

        // 認証情報を取得する
        $authFile = $this->_config->getAuthFile();
        if (file_exists($authFile)) {
            $this->_authInfo = unserialize(file_get_contents($authFile));
        } else {
            $this->_authInfo = array(
                 'oauth_access_token' => null
                ,'oauth_access_token_secret' => null
            );
        }

    }

    /**
     * 認証処理を実行する
     */
    public function authorize() {
    	try {
    		session_start();

            $this->_oauth = new HTTP_OAuth_Consumer(
                                $this->_config->getConsumerKey(),
                                $this->_config->getConsumerSecret()
            );
            $http_request = new HTTP_Request2();
            $http_request->setConfig('ssl_verify_peer', false);
            $consumer_request = new HTTP_OAuth_Consumer_Request();
            $consumer_request->accept($http_request);
            $this->_oauth->accept($consumer_request);

            if (!empty($_REQUEST['oauth_token']) && $_SESSION['oauth_state'] === 'start') {
                // 認証情報保存
                $this->saveAuthorize();
            }

            if ($this->isAuthorized()) {
                // 認証済みの場合
                $this->_oauth->setToken($this->_authInfo['oauth_access_token']);
                $this->_oauth->setTokenSecret($this->_authInfo['oauth_access_token_secret']);
            } else {
                // 未承認の場合
                $this->callAuthorization();
            }
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    /**
     * 認証が行われているかどうかをチェックする
     * @return boolean 認証済みはTrue、認証されてない場合はFalse
     */
	public function isAuthorized() {
        if ($this->_authInfo['oauth_access_token'] === null
        || $this->_authInfo['oauth_access_token_secret'] === null) {
        	return false;
        }
        return true;
	}

	/**
	 * 認証情報を保存する
	 */
    private function saveAuthorize() {
        // トークンが未取得の場合
        $this->_oauth->setToken($_SESSION['oauth_request_token']);
        $this->_oauth->setTokenSecret($_SESSION['oauth_request_token_secret']);

        $verifier = $_GET['oauth_verifier'];
        $token = $this->_oauth->getAccessToken('https://twitter.com/oauth/access_token', $verifier);

        // トークンを保存
        $this->_authInfo['oauth_access_token'] = $this->_oauth->getToken();
        $this->_authInfo['oauth_access_token_secret'] = $this->_oauth->getTokenSecret();

        $_SESSION['oauth_state'] === 'authorized';

        $authFile = $this->_config->getAuthFile();
        file_put_contents($authFile, serialize($this->_authInfo));

        // リダイレクト
        header('Location: ' . $this->_config->getCallback());
    }

    /**
     *
     * @return unknown_type
     */
    private function callAuthorization() {
        $this->_oauth->getRequestToken($this->_requestTokenUri, $this->_config->getCallback());

        $_SESSION['oauth_request_token'] = $this->_oauth->getToken();
        $_SESSION['oauth_request_token_secret'] = $this->_oauth->getTokenSecret();
        $_SESSION['oauth_state'] = 'start';

        $requri = $this->_oauth->getAuthorizeURL('https://twitter.com/oauth/authorize');

        if ($this->_auth_page !== null) {
            require_once($this->_auth_page);
        } else {
            printf('<a href="%s">認証</a>', $requri);
        }
        die(0);
    }
}