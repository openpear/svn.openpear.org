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

/**
 * OAuth signature class for service provider.
 *
 * @category HTTP
 * @package  OAuthProvider
 * @author   Tetsuya Yoshida <tetu@eth0.jp>
 * @license  http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version  1.0.4
 * @link     http://openpear.org/package/HTTP_OAuthProvider
 */
class HTTP_OAuthProvider_Signature_HMAC_SHA1 extends HTTP_OAuthProvider_Signature
{
    /**
     * checkSignature
     * 
     * Finds whether a $oauth_signature is a valid string
     * 
     * @return String
     */
    public function checkSignature()
    {
        $signature = $this->getSignature();
        $req_signature = $this->provider->getRequest()->getSignature();
        if ($signature==$req_signature) {
            return true;
        }
        throw new HTTP_OAuthProvider_Exception('401 Unauthorized', 401);
    }

    /**
     * getSignature
     * 
     * Return a signature
     * 
     * @return String
     */
    protected function getSignature()
    {
        // signature base string
        $base_string = $this->getSignatureBaseString();

        // consumer secret
        $secret = $this->provider->getConsumer()->getSecret();

        // token secret
        $token_secret = '';
        $token = $this->provider->getRequest()->getParameter('oauth_token');
        if ($token) {
            $store = $this->provider->getStore();
            try {
                $store->loadToken($this->provider);
            } catch(Exception $e) {
            }
            $token_secret = $store->getSecret();
        }

        // signature
        $key = sprintf('%s&%s', $secret, $token_secret);
        return base64_encode(hash_hmac('sha1', $base_string, $key, true));
    }
}
