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
 * @version   1.0.7
 * @link      http://openpear.org/package/HTTP_OAuthProvider
 */

/**
 * OAuth token store class for service provider.
 *
 * @category HTTP
 * @package  OAuthProvider
 * @author   Tetsuya Yoshida <tetu@eth0.jp>
 * @license  http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version  1.0.7
 * @link     http://openpear.org/package/HTTP_OAuthProvider
 */
abstract class HTTP_OAuthProvider_Store
{
    protected $row = null;

    /**
     * factory
     * 
     * Generate the HTTP_OAuthProvider_Store instance.
     * 
     * @param String $driver  Store driver name.
     * @param Array  $options Store options.
     * 
     * @return HTTP_OAuthProvider_Store
     */
    public static function factory($driver='CacheLite', array $options=array())
    {
        $driver = str_replace('-', '_', $driver);

        $file = sprintf('%s/Store/%s.php', dirname(__FILE__), $driver);
        $class = sprintf('HTTP_OAuthProvider_Store_%s', $driver);
        if (!is_file($file)) {
            $message = 'Store driver is not found';
            throw new HTTP_OAuthProvider_Store_Exception($message, 500);
        }
        include_once $file;
        if (class_exists($class)) {
            if (is_subclass_of($class, 'HTTP_OAuthProvider_Store')) {
                return new $class($options);
            }
        }
        $message = 'Store driver is not found';
        throw new HTTP_OAuthProvider_Store_Exception($message, 500);
    }


    /* initialize token */

    /**
     * issueRequestToken
     * 
     * Issue a new request token.
     * 
     * @param HTTP_OAuthProvider $provider A HTTP_OAuthProvider instance.
     * 
     * @return void
     */
    public function issueRequestToken(HTTP_OAuthProvider $provider)
    {
        $consumer = $provider->getConsumer();
        $request = $provider->getRequest();
        $this->row = array(
            'type'          => 'request',
            'consumer_key'  => $consumer->getKey(),
            'callback'      => $request->getParameter('oauth_callback'),
            'timestamp'     => $request->getParameter('oauth_timestamp'),
            'token'         => self::makeToken(),
            'secret'        => self::makeSecret()
        );
    }

    /**
     * loadToken
     * 
     * Load a token by store.
     * 
     * @param HTTP_OAuthProvider $provider A HTTP_OAuthProvider instance.
     * 
     * @return String
     * 
     * @throws HTTP_OAuthProvider_Exception If a token is not found.
     */
    public function loadToken(HTTP_OAuthProvider $provider)
    {
        $consumer = $provider->getConsumer();
        $request = $provider->getRequest();
        $token = $request->getParameter('oauth_token');
        $this->row = $this->get($token);
        if ($this->row) {
            return $this->getType();
        }
        throw new HTTP_OAuthProvider_Exception('404 A token is not found', 404);
    }


    /* update token */

    /**
     * authorizeToken
     * 
     * Authorize request token.
     * 
     * @param String $user_id User who authorizes access to protected resources.
     * 
     * @return String
     * 
     * @throws HTTP_OAuthProvider_Exception If a request token is not found.
     */
    public function authorizeToken($user_id)
    {
        if (isset($this->row['type']) && $this->row['type']=='request') {
            $this->row['type'] = 'authorize';
            $this->row['verifier'] = self::makeVerifier();
            $this->row['user_id'] = $user_id;
            return;
        }
        $message ='404 A request token is not found';
        throw new HTTP_OAuthProvider_Exception($message, 404);
    }

    /**
     * exchangeAccessToken
     * 
     * Change from authorized request token to access token.
     * 
     * @return void
     * 
     * @throws HTTP_OAuthProvider_Exception If an authorize token is not found.
     */
    public function exchangeAccessToken()
    {
        if (isset($this->row['type']) && $this->row['type']=='authorize') {
            $this->row['type'] = 'access';
            $this->row['token'] = self::makeToken();
            return;
        }
        $message = '404 An authorize token is not found';
        throw new HTTP_OAuthProvider_Exception($message, 404);
    }


    /* Get method */

    /**
     * getParameter
     * 
     * Return a row parameter.
     * 
     * @param String $key The key to the row.
     * 
     * @return String
     */
    public function getParameter($key)
    {
        if (isset($this->row[$key])) {
            return $this->row[$key];
        }
        return null;
    }

    /**
     * getType
     * 
     * Return a token type.
     * 
     * @return String
     */
    public function getType()
    {
        return $this->getParameter('type');
    }

    /**
     * getConsumerKey
     * 
     * Return a consumer key.
     * 
     * @return String
     */
    public function getConsumerKey()
    {
        return $this->getParameter('consumer_key');
    }

    /**
     * getCallback
     * 
     * Return a callback url.
     * 
     * @return String
     */
    public function getCallback()
    {
        return $this->getParameter('callback');
    }

    /**
     * getTimestamp
     * 
     * Return a timestamp.
     * 
     * @return String
     */
    public function getTimestamp()
    {
        return $this->getParameter('timestamp');
    }

    /**
     * getToken
     * 
     * Return a token.
     * 
     * @return String
     */
    public function getToken()
    {
        return $this->getParameter('token');
    }

    /**
     * getSecret
     * 
     * Return a token secret.
     * 
     * @return String
     */
    public function getSecret()
    {
        return $this->getParameter('secret');
    }

    /**
     * getVerifier
     * 
     * Return a token secret.
     * 
     * @return String
     */
    public function getVerifier()
    {
        return $this->getParameter('verifier');
    }

    /**
     * getUserID
     * 
     * Return a user id.
     * 
     * @return String
     */
    public function getUserID()
    {
        return $this->getParameter('user_id');
    }


    /* abstract */

    /**
     * __construct
     * 
     * Generate the HTTP_OAuthProvider_Store instance.
     * 
     * @param Array $options Store options.
     * 
     * @return HTTP_OAuthProvider_Store
     */
    abstract public function __construct(array $options=array());

    /**
     * get
     * 
     * Retrieve a token.
     * 
     * @param String $token A token to retrive.
     * 
     * @return Array
     */
    abstract public function get($token);

    /**
     * save
     * 
     * Save a token.
     * 
     * @return Boolean
     */
    abstract public function save();

    /**
     * remove
     * 
     * Remove a token.
     * 
     * @return Boolean
     */
    abstract public function remove();


    /* utils */

    /**
     * makeToken
     * 
     * create random string.
     * 
     * @return String
     */
    public static function makeToken()
    {
        $token = '';
        for ($i=0; $i<3; $i++) {
            $m = mt_rand(0, 1) ? 'sha1' : 'md5';
            $token .= $m($token.microtime().mt_rand(), 1);
        }
        $token = base64_encode($token);
        $token = str_replace(array('=', '/', '+'), array('', '', ''), $token);
        return $token;
    }

    /**
     * makeSecret
     * 
     * create random string.
     * 
     * @return String
     */
    public static function makeSecret()
    {
        return self::makeToken();
    }

    /**
     * makeVerifier
     * 
     * create random string.
     * 
     * @return String
     */
    public static function makeVerifier()
    {
        return self::makeToken();
    }
}
