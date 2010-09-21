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
 * @version   1.1.0
 * @link      http://openpear.org/package/HTTP_OAuthProvider
 */

/**
 * OAuth consumer class for service provider.
 *
 * @category HTTP
 * @package  OAuthProvider
 * @author   Tetsuya Yoshida <tetu@eth0.jp>
 * @license  http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version  1.1.0
 * @link     http://openpear.org/package/HTTP_OAuthProvider
 */
class HTTP_OAuthProvider_Consumer
{
    protected $row = null;

    /**
     * __construct
     * 
     * Generate the HTTP_OAuthProvider_Consumer instance.
     * 
     * @param Array $row Consumer data.
     * 
     * @return HTTP_OAuthProvider_Consumer
     */
    public function __construct(array $row=array())
    {
        $this->row = $row;
    }

    /**
     * getParameter
     * 
     * Returns either the named parameter.
     * 
     * @param String $key Name of parameter to return.
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
     * getKey
     * 
     * Return a consumer key.
     * 
     * @return String
     */
    public function getKey()
    {
        return $this->getParameter('key');
    }

    /**
     * getSecret
     * 
     * Return a consumer secret.
     * 
     * @return String
     */
    public function getSecret()
    {
        return $this->getParameter('secret');
    }

    /**
     * getPublicKey
     * 
     * Return a consumer public key.
     * 
     * @return String
     */
    public function getPublicKey()
    {
        return $this->getParameter('publickey');
    }
}
