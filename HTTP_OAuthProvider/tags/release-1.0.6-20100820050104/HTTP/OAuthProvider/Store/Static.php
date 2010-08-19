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
class HTTP_OAuthProvider_Store_Static extends HTTP_OAuthProvider_Store
{
    /**
     * __construct
     * 
     * Generate the HTTP_OAuthProvider_Store_Static instance.
     * 
     * @param Array $options Store options.
     * 
     * @return HTTP_OAuthProvider_Store_Static
     * 
     * @throws HTTP_OAuthProvider_Store_Exception If failing in the make directory.
     */
    public function __construct(array $options=array())
    {
        $this->row = $options;
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
        return $this->row;
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
        return false;
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
        return false;
    }
}
