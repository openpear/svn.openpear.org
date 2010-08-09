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
 * @version   Release: 0.1
 * @link      http://pear.php.net/package/PackageName
 */
require_once('HTTP/OAuthProvider/Request.php');
require_once('HTTP/OAuthProvider/Signature.php');
require_once('HTTP/OAuthProvider/Consumer.php');
require_once('HTTP/OAuthProvider/Store.php');
require_once('HTTP/OAuthProvider/Exception.php');

/**
 * OAuth authentication class for service provider.
 *
 * @category HTTP
 * @package  OAuthProvider
 * @author   Tetsuya Yoshida <tetu@eth0.jp>
 * @license  http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version  Release: 0.1
 * @link     http://pear.php.net/package/PackageName
 */
class HTTP_OAuthProvider
{
    // OAuth parameters
    protected static $_2l_params = array(
        'oauth_consumer_key',
        'oauth_signature_method',
        'oauth_timestamp',
        'oauth_nonce',
        'oauth_version'
    );
    protected static $_3l_request_params = array(
        'oauth_callback'
    );
    protected static $_3l_authorize_params = array(
        'oauth_token'
    );
    protected static $_3l_access_params = array(
        'oauth_token',
        'oauth_verifier'
    );
    protected static $_3l_resource_params = array(
        'oauth_token'
    );

    // Handler
    protected $_find_consumer_handler = null;
    protected $_check_timestamp_handler = null;

    // Result
    protected $_success = null;
    protected $_request = null;
    protected $_consumer = null;
    protected $_store = null;


    /* Construct */

    /**
     * __construct
     * 
     * Generate the OAuthProvider instance
     * 
     * @return OAuthProvider
     */
    public function __construct()
    {
        $this->_request = new HTTP_OAuthProvider_Request();
        // set default handler
        $this->setCheckTimestampHandler(array('HTTP_OAuthProvider', 'checkTimestampHandler'));
        $this->setFindConsumerHandler(array('HTTP_OAuthProvider', 'findConsumerHandler'));
    }


    /**
     * authenticate
     * 
     * Authenticate consumer by 2legged OAuth
     * 
     * @return Boolean
     */
    public function authenticate()
    {
        // already executed
        if (isset($this->_success)) {
            return $this->_success;
        }

        // start authentication
        $this->_success = false;

        // check request
        $this->getRequest()->checkParameters(self::$_2l_params, true);
        $this->getRequest()->checkBodyHash();
        if (!$this->checkTimestamp($this->getRequest()->getParameter('oauth_timestamp'))) {
            throw new HTTP_OAuthProvider_Exception('401 Invalid timestamp', 401);
        }

        // get consumer
        $this->_consumer = $this->findConsumer($this->getRequest()->getParameter('oauth_consumer_key'));
        if (!$this->_consumer) {
            throw new HTTP_OAuthProvider_Exception('401 Consumer is not found', 401);
        }
        if (!$this->_consumer instanceof HTTP_OAuthProvider_Consumer) {
            throw new HTTP_OAuthProvider_Exception('500 FindConsumerHandler did not return HTTP_OAuthProvider_Consumer instance', 500);
        }

        // check signature method
        $sig_method = $this->getRequest()->getParameter('oauth_signature_method');
        $sig_method = str_replace('-', '_', $sig_method);
        $sig_file = sprintf('%s/OAuthProvider/Signature/%s.php', dirname(__FILE__), $sig_method);
        if (!is_file($sig_file)) {
            throw new HTTP_OAuthProvider_Exception('400 Signature method is not implemented', 400);
        }
        require_once($sig_file);
        $sig_class = sprintf('HTTP_OAuthProvider_Signature_%s', $sig_method);
        if (!class_exists($sig_class) || !is_subclass_of($sig_class, 'HTTP_OAuthProvider_Signature')) {
            throw new HTTP_OAuthProvider_Exception('400 Signature method is not implemented', 400);
        }

        // check signature
        $sig = new $sig_class($this);
        $sig->checkSignature();

        // Success
        $this->_success = true;
        return true;
    }


    /* 3legged OAuth */

    /**
     * issueRequestToken
     * 
     * issue request token to consumer
     * 
     * @return String
     */
    public function issueRequestToken()
    {
        // 2legged OAuth
        $this->authenticate();

        // Check parameter
        $this->getRequest()->checkParameters(self::$_3l_request_params);
        $callback = $this->getRequest()->getParameter('oauth_callback');
        if (!preg_match('#^https?://.#', $callback)) {
            throw new HTTP_OAuthProvider_Exception('400 oauth_callback is not correct: '.$callback, 400);
        }

        // make token
        $store = $this->getStore();
        $store->issueRequestToken($this);
        $ok = $store->save();
        if (!$ok) {
            throw new HTTP_OAuthProvider_Exception('500 Store error', 500);
        }

        // response
        $resp = array(
            'oauth_token' => $store->getToken(),
            'oauth_token_secret' => $store->getSecret(),
            'oauth_callback_confirmed' => 'true'
        );
        return http_build_query($resp);
    }

    /**
     * existsRequestToken
     * 
     * Checks if the request token exists
     * 
     * @return Boolean
     */
    public function existsRequestToken()
    {
        $store = $this->getStore();
        try {
            // load token
            $type = $store->loadToken($this);

            // find consumer
            $this->_consumer = $this->findConsumer($store->getConsumerKey());

            // return result
            if ($type=='request') {
                return true;
            }
        } catch(Exception $e) {
        }
        return false;
    }


    /**
     * authorizeToken
     * 
     * Authorize consumer to access protected resources
     * Redirect user to returnd callback_url
     * 
     * @return String
     */
    public function authorizeToken($user_id, $agree)
    {
        // check parameter
        $this->getRequest()->checkParameters(self::$_3l_authorize_params);

        // load token
        $store = $this->getStore();
        $type = $store->loadToken($this);
        if ($type!='request') {
            throw new HTTP_OAuthProvider_Exception('404 Not found request token in store', 404);
        }

        // find consumer
        $this->_consumer = $this->findConsumer($store->getConsumerKey());

        // agree
        if ($agree) {
            // update token
            $store->authorizeToken($user_id);
            $ok = $store->save();
            if (!$ok) {
                throw new HTTP_OAuthProvider_Exception('500 Store error', 500);
            }

            // build callback url
            $callback = $store->getCallback();
            @list($callback_url, $callback_query_str) = explode('?', $callback);
            parse_str($callback_query_str, $callback_query);
            $callback_query['oauth_token'] = $store->getToken();
            $callback_query['oauth_verifier'] = $store->getVerifier();
            return $callback_url.'?'.http_build_query($callback_query);

        // disagree
        } else {
            // delete token
            $ok = $store->remove();
            if (!$ok) {
                throw new HTTP_OAuthProvider_Exception('500 Store error', 500);
            }

            // return callback url
            return $store->getCallback();
        }
    }

    /**
     * exchangeAccessToken
     * 
     * issue access token to consumer
     * 
     * @return String
     */
    public function exchangeAccessToken()
    {
        // 2legged OAuth
        $this->authenticate();

        // Check parameter
        $this->getRequest()->checkParameters(self::$_3l_access_params);

        // load token
        $store = $this->getStore();
        $type = $store->loadToken($this);

        // check consumer
        $request_consumer = $this->getConsumer()->getKey();
        $token_consumer = $store->getConsumerKey();
        if ($type!='authorize' || $token_consumer!=$request_consumer) {
            throw new HTTP_OAuthProvider_Exception('404 Not found authorized request token in store', 404);
        }

        // delete authorized request token
        $ok = $store->remove();
        if (!$ok) {
            throw new HTTP_OAuthProvider_Exception('500 Store error', 500);
        }

        // change from authorized request token to access token
        $store->exchangeAccessToken();
        $ok = $store->save();
        if (!$ok) {
            throw new HTTP_OAuthProvider_Exception('500 Store error', 500);
        }

        // response
        $resp = array(
            'oauth_token' => $store->getToken(),
            'oauth_token_secret' => $store->getSecret()
        );
        return http_build_query($resp);
    }

    /**
     * authenticate3L
     * 
     * Authenticate consumer by 3legged OAuth
     * 
     * @return Boolean
     */
    public function authenticate3L()
    {
        // 2legged OAuth
        $this->authenticate();

        // Check parameter
        $this->getRequest()->checkParameters(self::$_3l_resource_params);

        // load token
        $store = $this->getStore();
        $type = $store->loadToken($this);

        // check consumer
        $request_consumer = $this->getConsumer()->getKey();
        $token_consumer = $store->getConsumerKey();
        if ($type!='access' || $token_consumer!=$request_consumer) {
            throw new HTTP_OAuthProvider_Exception('404 Not found access token in store', 404);
        }
        return true;
    }


    /* Set handler */

    public function setFindConsumerHandler($handler)
    {
        if (!is_callable($handler)) {
            throw new HTTP_OAuthProvider_Exception('500 FindConsumerHandler is not callable', 500);
        }
        $this->_find_consumer_handler = $handler;
    }

    public function setCheckTimestampHandler($handler)
    {
        if (!is_callable($handler)) {
            throw new HTTP_OAuthProvider_Exception('500 CheckTimestampHandler is not callable', 500);
        }
        $this->_check_timestamp_handler = $handler;
    }

    public function setStore(HTTP_OAuthProvider_Store $store)
    {
        $this->_store = $store;
    }


    /* Get method */

    /**
     * isSuccess
     * 
     * Return an authentication result
     * 
     * @return Boolean
     */
    public function isSuccess()
    {
        return $this->_success;
    }

    public function getConsumer()
    {
        return $this->_consumer;
    }

    public function getRequest()
    {
        return $this->_request;
    }

    public function getStore()
    {
        if (!isset($this->_store)) {
            $this->_store = HTTP_OAuthProvider_Store::factory();
        }
        return $this->_store;
    }


    /* exec handler */

    public function checkTimestamp($timestamp)
    {
        return call_user_func($this->_check_timestamp_handler, $timestamp);
    }

    public function findConsumer($consumer_key)
    {
        return call_user_func($this->_find_consumer_handler, $consumer_key);
    }


    /* Handler */

    /**
     * checkTimestampHandler
     * 
     * Check timestamp
     * 
     * @param String $oauth_timestamp Received oauth_timestamp
     * 
     * @return Boolean
     */
    public static function checkTimestampHandler($timestamp)
    {
        $req_time = (int)$timestamp;
        $parmit_past = time()-300;
        $parmit_future = time()+5;
        if ($parmit_past<$req_time && $req_time<$parmit_future) {
            return true;
        }
        return false;
    }

    public static function findConsumerHandler($consumer_key)
    {
        throw new HTTP_OAuthProvider_Exception('500 FindConsumerHandler is not set', 500);
    }
}
