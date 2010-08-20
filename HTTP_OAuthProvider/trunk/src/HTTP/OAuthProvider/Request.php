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
require_once 'HTTP/OAuthProvider/Exception.php';

/**
 * Parse request class for OAuthProvider package
 *
 * @category HTTP
 * @package  OAuthProvider
 * @author   Tetsuya Yoshida <tetu@eth0.jp>
 * @license  http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version  1.0.7
 * @link     http://openpear.org/package/HTTP_OAuthProvider
 */
class HTTP_OAuthProvider_Request
{
    protected static $instance = null;
    protected $method = null;
    protected $header = null;
    protected $params = null;
    protected $signature = null;
    protected $body = null;


    /* construct */

    /**
     * __construct
     * 
     * Generate the HTTP_OAuthProvider_Request instance.
     * 
     * @return HTTP_OAuthProvider_Request
     */
    protected function __construct()
    {
        $this->initialize();
    }

    /**
     * getInstance
     * 
     * Generate the HTTP_OAuthProvider_Request instance.
     * 
     * @return HTTP_OAuthProvider_Request
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new HTTP_OAuthProvider_Request();
        }
        return self::$instance;
    }


    /* getter */

    /**
     * getMethod
     * 
     * Return a request method.
     * 
     * @return String
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * getHeader
     * 
     * Returns either the named header or all request headers.
     * 
     * @param String $key Name of header to return.
     * 
     * @return String
     */
    public function getHeader($key=null)
    {
        if (isset($key)) {
            $key = strtoupper($key);
            if (isset($this->header[$key])) {
                return $this->header[$key];
            }
            return null;
        }
        return $this->header;
    }

    /**
     * getParameter
     * 
     * Returns either the named parameter or all request parameters.
     * 
     * @param String $key Name of parameter to return.
     * 
     * @return String
     */
    public function getParameter($key=null)
    {
        if (isset($key)) {
            if (isset($this->params[$key])) {
                return $this->params[$key];
            }
            return null;
        }
        return $this->params;
    }

    /**
     * getSignature
     * 
     * Return a request signature.
     * 
     * @return String
     */
    public function getSignature()
    {
        return $this->signature;
    }

    /**
     * getBody
     * 
     * Return a request body.
     * 
     * @return String
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * getBodyHash
     * 
     * Return a valid request body hash.
     * 
     * @return String
     */
    public function getBodyHash()
    {
        return base64_encode(sha1($this->getBody(), true));
    }


    /* check */

    /**
     * checkParameters
     * 
     * Check necessary parameters
     * 
     * @param Array $keys Necessary parameters.
     * 
     * @return Boolean
     * 
     * @throws HTTP_OAuthProvider_Exception If a necessary parameter doesn't exist.
     */
    public function checkParameters($keys)
    {
        $noparam = array();
        foreach ($keys as $key) {
            if ($key=='oauth_signature') {
                if (!$this->getSignature()) {
                    $noparam[] = $key;
                }
            } else {
                if (!$this->getParameter($key)) {
                    $noparam[] = $key;
                }
            }
        }
        if (0<count($noparam)) {
            $message = sprintf(
                '400 OAuth parameter(s) does not exist: %s',
                implode(', ', $noparam)
            );
            throw new HTTP_OAuthProvider_Exception($message, 400);
        }
        return true;
    }

    /**
     * checkBodyHash
     * 
     * Check oauth_body_hash
     * 
     * @return Boolean
     * 
     * @throws HTTP_OAuthProvider_Exception If oauth_body_hash is not valid.
     */
    public function checkBodyHash()
    {
        if ($this->getHeader('CONTENT-TYPE')!='application/x-www-form-urlencoded') {
            if (0<strlen($this->getBody())) {
                if (is_null($this->getParameter('oauth_body_hash'))) {
                    $message = '400 OAuth parameter(s) does not exist: oauth_body_hash';
                    throw new HTTP_OAuthProvider_Exception($message, 400);
                }
                if ($this->getParameter('oauth_body_hash')!=$this->getBodyHash()) {
                    $message = '401 Body Hash is not valid';
                    throw new HTTP_OAuthProvider_Exception($message, 401);
                }
            }
        }
        return true;
    }

    /**
     * checkTimestamp
     * 
     * Check request timestamp.
     * 
     * @param Integer $valid_past   Valid past time
     * @param Integer $valid_future Valid future time
     * 
     * @return Boolean
     * 
     * @throws HTTP_OAuthProvider_Exception If oauth_timestamp is not valid.
     */
    public function checkTimestamp($valid_past, $valid_future)
    {
        $timestamp = (int)$this->getParameter('oauth_timestamp');
        $valid_past = time()-$valid_past;
        $valid_future = time()+$valid_future;
        if ($valid_past<$timestamp && $timestamp<$valid_future) {
            return true;
        }
        $message = '401 oauth_timestamp is not valid';
        throw new HTTP_OAuthProvider_Exception($message, 401);
    }


    /* private method */

    /**
     * initialize
     * 
     * Set request parameters.
     * 
     * @return void
     */
    protected function initialize()
    {
        // Header
        $this->header = array_change_key_case(apache_request_headers(), CASE_UPPER);

        // HTTP Method
        $this->method = $_SERVER['REQUEST_METHOD'];
        if (isset($this->header['HTTP_X_HTTP_METHOD_OVERRIDE'])) {
            $this->method = $this->header['HTTP_X_HTTP_METHOD_OVERRIDE'];
        }

        // Parameters
        $this->parseRequestParameters();

        // Body
        $this->body = file_get_contents('php://input');
    }

    /**
     * parseRequestParameters
     * 
     * Parse request parameters.
     * 
     * @return void
     */
    protected function parseRequestParameters()
    {
        // GET
        $params = $_GET;

        // POST
        if ($this->getHeader('CONTENT-TYPE')=='application/x-www-form-urlencoded') {
            $params = array_merge($params, $_POST);
        }

        // Header
        $header = $this->getHeader('AUTHORIZATION');
        if (strpos($header, 'OAuth ')===0) {
            preg_match_all('/([^"= ]*)="([^"]*)"/', $header, $match, PREG_SET_ORDER);
            foreach ($match as $m) {
                $params[urldecode($m[1])] = urldecode($m[2]);
            }
        }

        // Remove realm
        if (isset($params['realm'])) {
            unset($params['realm']);
        }

        // oauth_signature
        $signature = null;
        if (isset($params['oauth_signature'])) {
            $signature = $params['oauth_signature'];
            unset($params['oauth_signature']);
        }

        $this->params = $params;
        $this->signature = $signature;
    }
}
