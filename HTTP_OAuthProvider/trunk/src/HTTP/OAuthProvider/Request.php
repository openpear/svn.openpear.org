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
require_once 'HTTP/OAuthProvider/Exception.php';

/**
 * Parse request class for OAuthProvider package
 *
 * @category HTTP
 * @package  OAuthProvider
 * @author   Tetsuya Yoshida <tetu@eth0.jp>
 * @license  http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version  1.0.4
 * @link     http://openpear.org/package/HTTP_OAuthProvider
 */
class HTTP_OAuthProvider_Request
{
    protected $method = null;
    protected $header = null;
    protected $params = null;
    protected $signature = null;
    protected $body = null;


    /* construct */

    /**
     * __construct
     * 
     * Generate the HTTP_OAuthProvider_Request instance
     * 
     * @return HTTP_OAuthProvider_Request
     */
    public function __construct()
    {
        $this->initialize();
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
     * Return a request header.
     * 
     * @param String $key The key to the request header.
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
     * Return a request parameter.
     * 
     * @param String $key The key to the request parameter.
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
     * @param Array   $keys            Necessary parameters
     * @param Boolean $check_signature is signature checked?
     * 
     * @return Boolean
     */
    public function checkParameters($keys, $check_signature=false)
    {
        $noparam = array();
        foreach ($keys as $key) {
            if (!$this->getParameter($key)) {
                $noparam[] = $key;
            }
        }
        if ($check_signature && !$this->getSignature()) {
            $noparam[] = 'oauth_signature';
        }
        if (0<count($noparam)) {
            $noparam = implode(', ', $noparam);
            $message = sprintf('400 OAuth parameter(s) does not exist: ', $noparam);
            throw new HTTP_OAuthProvider_Exception($message, 400);
        }
        return true;
    }

    /**
     * checkBodyHash
     * 
     * check oauth_body_hash
     * 
     * @return Boolean
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
     * Check timestamp
     * 
     * @param Integer $valid_past   Valid past time
     * @param Integer $valid_future Valid future time
     * 
     * @return Boolean
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
     * Set request parameters
     * 
     * @return Array
     */
    protected function initialize()
    {
        static $initialized = false;
        if ($initialized) {
            return;
        }

        // header
        $this->header = array_change_key_case(apache_request_headers(), CASE_UPPER);

        // HTTP Method
        $this->method = $_SERVER['REQUEST_METHOD'];
        if (isset($this->header['HTTP_X_HTTP_METHOD_OVERRIDE'])) {
            $this->method = $this->header['HTTP_X_HTTP_METHOD_OVERRIDE'];
        }

        // parameters
        list($this->params, $this->signature) = $this->parseRequestParameters();

        // body
        $this->body = file_get_contents('php://input');

        $initialized = true;
    }

    /**
     * parseRequestParameters
     * 
     * Parse request parameters
     * 
     * @return Array
     */
    protected function parseRequestParameters()
    {
        // get
        $params = $_GET;

        // post
        if ($this->getHeader('CONTENT-TYPE')=='application/x-www-form-urlencoded') {
            $params = array_merge($params, $_POST);
        }

        // header
        if (strpos($this->getHeader('AUTHORIZATION'), 'OAuth ')===0) {
            $params_tmp = preg_split("/,[\r\n\s]*/", $this->header['AUTHORIZATION']);
            foreach ($params_tmp as $v) {
                if (preg_match('/^([^"= ]+)="([^"]*)"$/', $v, $m)) {
                    $params[urldecode($m[1])] = urldecode($m[2]);
                }
            }
        }
        if (isset($params['realm'])) {
            unset($params['realm']);
        }

        // oauth_signature
        $signature = null;
        if (isset($params['oauth_signature'])) {
            $signature = $params['oauth_signature'];
            unset($params['oauth_signature']);
        }

        return array($params, $signature);
    }
}
