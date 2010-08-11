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
 * @version   1.0.4
 * @link      http://openpear.org/package/HTTP_OAuthProvider
 */
require_once 'HTTP/OAuthProvider/Request.php';
require_once 'HTTP/OAuthProvider/Signature.php';
require_once 'HTTP/OAuthProvider/Consumer.php';
require_once 'HTTP/OAuthProvider/Store.php';
require_once 'HTTP/OAuthProvider/Exception.php';
require_once 'HTTP/OAuthProvider/Store/Exception.php';

/**
 * OAuth authentication class for service provider.
 *
 * @category HTTP
 * @package  OAuthProvider
 * @author   Tetsuya Yoshida <tetu@eth0.jp>
 * @license  http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version  1.0.4
 * @link     http://openpear.org/package/HTTP_OAuthProvider
 */
class HTTP_OAuthProvider
{
    // OAuth parameters
    protected static $PARAMS_2L = array(
        'oauth_consumer_key',
        'oauth_signature_method',
        'oauth_timestamp',
        'oauth_nonce',
        'oauth_version'
    );
    protected static $PARAMS_3L_REQUEST = array(
        'oauth_callback'
    );
    protected static $PARAMS_3L_AUTHORIZE = array(
        'oauth_token'
    );
    protected static $PARAMS_3L_ACCESS = array(
        'oauth_token',
        'oauth_verifier'
    );
    protected static $PARAMS_3L_RESOURCE = array(
        'oauth_token'
    );
    protected $valid_timestamp_past = 300;
    protected $valid_timestamp_future = 5;

    // Handler
    protected $fetch_consumer_handler = null;

    // Result
    protected $success = null;
    protected $request = null;
    protected $consumer = null;
    protected $store = null;


    /* Construct */

    /**
     * __construct
     * 
     * Generate the HTTP_OAuthProvider instance
     * 
     * @return HTTP_OAuthProvider
     */
    public function __construct()
    {
        $this->request = new HTTP_OAuthProvider_Request();
        // set default handler
        $fetch_consumer = array('HTTP_OAuthProvider', 'fetchConsumerHandler');
        $this->setFetchConsumerHandler($fetch_consumer);
    }


    /* 2legged OAuth */

    /**
     * authenticate
     * 
     * Authenticate consumer by 2legged OAuth.
     * 
     * @return Boolean
     */
    public function authenticate()
    {
        // already executed
        if (isset($this->success)) {
            return $this->success;
        }

        // start authentication
        $this->success = false;

        // check request
        $this->getRequest()->checkParameters(self::$PARAMS_2L, true);
        $this->getRequest()->checkBodyHash();
        $this->getRequest()->checkTimestamp(
            $this->valid_timestamp_past,
            $this->valid_timestamp_future
        );

        // get consumer
        $consumer_key = $this->getRequest()->getParameter('oauth_consumer_key');
        $this->consumer = $this->fetchConsumer($consumer_key);
        if (!$this->consumer) {
            throw new HTTP_OAuthProvider_Exception('401 Consumer is not found', 401);
        }
        if (!$this->consumer instanceof HTTP_OAuthProvider_Consumer) {
            $message = '500 FetchConsumerHandler did not return HTTP_OAuthProvider_Consumer instance';
            throw new HTTP_OAuthProvider_Exception($message, 500);
        }

        // check signature method
        $sig_method = $this->getRequest()->getParameter('oauth_signature_method');
        $sig_method = str_replace('-', '_', $sig_method);
        $sig_file = sprintf('%s/OAuthProvider/Signature/%s.php', dirname(__FILE__), $sig_method);
        if (!is_file($sig_file)) {
            throw new HTTP_OAuthProvider_Exception('400 Signature method is not implemented', 400);
        }
        include_once $sig_file;
        $sig_class = sprintf('HTTP_OAuthProvider_Signature_%s', $sig_method);
        if (!class_exists($sig_class) || !is_subclass_of($sig_class, 'HTTP_OAuthProvider_Signature')) {
            throw new HTTP_OAuthProvider_Exception('400 Signature method is not implemented', 400);
        }

        // check signature
        $sig = new $sig_class($this);
        $sig->checkSignature();

        // Success
        $this->success = true;
        return true;
    }


    /* 3legged OAuth */

    /**
     * issueRequestToken
     * 
     * Issue a request token to authenticated consumer.
     * 
     * @return String
     */
    public function issueRequestToken()
    {
        // 2legged OAuth
        $this->authenticate();

        // Check parameter
        $this->getRequest()->checkParameters(self::$PARAMS_3L_REQUEST);
        $callback = $this->getRequest()->getParameter('oauth_callback');
        if (!preg_match('#^https?://.#', $callback)) {
            $message = sprintf('400 oauth_callback is not correct: %s', $callback);
            throw new HTTP_OAuthProvider_Exception($message, 400);
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
     * Checks if the request token exists.
     * 
     * @return Boolean
     */
    public function existsRequestToken()
    {
        $store = $this->getStore();
        try {
            // load token
            $type = $store->loadToken($this);

            // fetch consumer
            $this->consumer = $this->fetchConsumer($store->getConsumerKey());

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
     * Authorize consumer to access user's protected resources.
     * Redirect user to returned callback_url.
     * 
     * @param String  $user_id User who authorizes access to protected resources.
     * @param Boolean $agree   Authorizes access to protected resources.
     * 
     * @return String
     */
    public function authorizeToken($user_id, $agree)
    {
        // check parameter
        $this->getRequest()->checkParameters(self::$PARAMS_3L_AUTHORIZE);

        // load token
        $store = $this->getStore();
        $type = $store->loadToken($this);
        if ($type!='request') {
            $message = '404 Not found request token in store';
            throw new HTTP_OAuthProvider_Exception($message, 404);
        }

        // fetch consumer
        $this->consumer = $this->fetchConsumer($store->getConsumerKey());

        if ($agree) {
            // agree

            // update token
            $store->authorizeToken($user_id);
            $ok = $store->save();
            if (!$ok) {
                throw new HTTP_OAuthProvider_Store_Exception('500 Store error', 500);
            }

            // build callback url
            $callback = $store->getCallback();
            @list($callback_url, $callback_query_str) = explode('?', $callback);
            parse_str($callback_query_str, $callback_query);
            $callback_query['oauth_token'] = $store->getToken();
            $callback_query['oauth_verifier'] = $store->getVerifier();
            return $callback_url.'?'.http_build_query($callback_query);

        } else {
            // disagree

            // delete token
            $ok = $store->remove();
            if (!$ok) {
                throw new HTTP_OAuthProvider_Store_Exception('500 Store error', 500);
            }

            // return callback url
            return $store->getCallback();
        }
    }

    /**
     * exchangeAccessToken
     * 
     * Issue a access token to consumer.
     * 
     * @return String
     */
    public function exchangeAccessToken()
    {
        // 2legged OAuth
        $this->authenticate();

        // Check parameter
        $this->getRequest()->checkParameters(self::$PARAMS_3L_ACCESS);

        // load token
        $store = $this->getStore();
        $type = $store->loadToken($this);

        // check consumer
        $request_consumer = $this->getConsumer()->getKey();
        $token_consumer = $store->getConsumerKey();
        if ($type!='authorize' || $token_consumer!=$request_consumer) {
            $message = '404 Not found authorized request token in store';
            throw new HTTP_OAuthProvider_Exception($message, 404);
        }

        // delete authorized request token
        $ok = $store->remove();
        if (!$ok) {
            throw new HTTP_OAuthProvider_Store_Exception('500 Store error', 500);
        }

        // change from authorized request token to access token
        $store->exchangeAccessToken();
        $ok = $store->save();
        if (!$ok) {
            throw new HTTP_OAuthProvider_Store_Exception('500 Store error', 500);
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
     * Authenticate consumer by 3legged OAuth.
     * 
     * @return Boolean
     */
    public function authenticate3L()
    {
        // 2legged OAuth
        $this->authenticate();

        // Check parameter
        $this->getRequest()->checkParameters(self::$PARAMS_3L_RESOURCE);

        // load token
        $store = $this->getStore();
        $type = $store->loadToken($this);

        // check consumer
        $request_consumer = $this->getConsumer()->getKey();
        $token_consumer = $store->getConsumerKey();
        if ($type!='access' || $token_consumer!=$request_consumer) {
            $message = '404 Not found access token in store';
            throw new HTTP_OAuthProvider_Exception($message, 404);
        }
        return true;
    }


    /* Set method */

    /**
     * setFetchConsumerHandler
     * 
     * Set the handler to fetch the consumer.
     * 
     * @param String $handler A name of the function to fetch the consumer
     * 
     * @return void
     */
    public function setFetchConsumerHandler($handler)
    {
        if (!is_callable($handler)) {
            $message = '500 FetchConsumerHandler is not callable';
            throw new HTTP_OAuthProvider_Exception($message, 500);
        }
        $this->fetch_consumer_handler = $handler;
    }

    /**
     * setStore
     * 
     * Set a HTTP_OAuthProvider_Store instance.
     * 
     * @param HTTP_OAuthProvider_Store $store HTTP_OAuthProvider_Store instance
     * 
     * @return void
     */
    public function setStore(HTTP_OAuthProvider_Store $store)
    {
        $this->store = $store;
    }

    /**
     * setValidTimestamp
     * 
     * Set a HTTP_OAuthProvider_Store instance.
     * 
     * @param Integer $valid_past   Valid past time
     * @param Integer $valid_future Valid future time
     * 
     * @return void
     */
    public function setValidTimestamp($valid_past, $valid_future=5)
    {
        $this->valid_timestamp_past = (int)$valid_past;
        $this->valid_timestamp_future = (int)$valid_future;
    }


    /* Get method */

    /**
     * getConsumer
     * 
     * Return a consumer instance.
     * If process is 'authorize', it is a consumer who issued the token.
     * Otherwise, a consumer is requested consumer.
     * 
     * @return Boolean
     */
    public function getConsumer()
    {
        return $this->consumer;
    }

    /**
     * getRequest
     * 
     * Return a HTTP_OAuthProvider_Request instance.
     * 
     * @return HTTP_OAuthProvider_Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * getStore
     * 
     * Return a HTTP_OAuthProvider_Store instance.
     * If store is not set, store is generated by using the setting of default.
     * 
     * @return HTTP_OAuthProvider_Store
     */
    public function getStore()
    {
        if (!isset($this->store)) {
            $this->store = HTTP_OAuthProvider_Store::factory();
        }
        return $this->store;
    }


    /* Handler */

    /**
     * fetchConsumer
     * 
     * Return a HTTP_OAuthProvider_Consumer instance for consumer_key if consumer_key is valid, else null.
     * Execute setFetchConsumerHandler() or override fetchConsumer().
     * 
     * @param String $consumer_key A consumer key to fetch.
     * 
     * @return HTTP_OAuthProvider_Consumer
     */
    protected function fetchConsumer($consumer_key)
    {
        return call_user_func($this->fetch_consumer_handler, $consumer_key);
    }

    /**
     * fetchConsumerHandler
     * 
     * Return a fetched HTTP_OAuthProvider_Consumer instance.
     * Execute setFetchConsumerHandler() or override fetchConsumer().
     * 
     * @param String $consumer_key A consumer key to fetch.
     * 
     * @return HTTP_OAuthProvider_Consumer
     */
    public static function fetchConsumerHandler($consumer_key)
    {
        $message = '500 FetchConsumerHandler is not set';
        throw new HTTP_OAuthProvider_Exception($message, 500);
    }
}
