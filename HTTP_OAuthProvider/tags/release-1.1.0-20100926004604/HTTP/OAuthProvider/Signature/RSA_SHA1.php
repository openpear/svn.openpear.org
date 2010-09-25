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
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version   1.1.0
 * @link      http://openpear.org/package/HTTP_OAuthProvider
 */

/**
 * OAuth signature class for service provider.
 *
 * @category HTTP
 * @package  OAuthProvider
 * @author   Tetsuya Yoshida <tetu@eth0.jp>
 * @license  http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version  1.1.0
 * @link     http://openpear.org/package/HTTP_OAuthProvider
 */
class HTTP_OAuthProvider_Signature_RSA_SHA1 extends HTTP_OAuthProvider_Signature
{
    /**
     * checkSignature
     * 
     * Finds whether a $oauth_signature is a valid string.
     * 
     * @return String
     * 
     * @throws HTTP_OAuthProvider_Exception If oauth_signature is not valid.
     */
    public function checkSignature()
    {
        $public_key = $this->provider->getConsumer()->getPublicKey();
        $base_string = $this->getSignatureBaseString();
        $signature = base64_decode($this->provider->getRequest()->getSignature());
        if ($public_key) {
            $publickeyid = openssl_get_publickey($public_key);
            $ok = openssl_verify($base_string, $signature, $publickeyid);
            openssl_free_key($publickeyid);
            if ($ok) {
                return true;
            }
        }
        throw new HTTP_OAuthProvider_Exception('401 Unauthorized', 401);
    }
}
