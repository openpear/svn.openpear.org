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
 * @version   1.0.5
 * @link      http://openpear.org/package/HTTP_OAuthProvider
 */

/**
 * OAuth signature class for service provider.
 *
 * @category HTTP
 * @package  OAuthProvider
 * @author   Tetsuya Yoshida <tetu@eth0.jp>
 * @license  http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version  1.0.5
 * @link     http://openpear.org/package/HTTP_OAuthProvider
 */
abstract class HTTP_OAuthProvider_Signature
{
    protected $provider = null;


    /* construct */

    /**
     * __construct
     * 
     * Generate the HTTP_OAuthProvider_Signature instance.
     * 
     * @param HTTP_OAuthProvider $provider A HTTP_OAuthProvider instance.
     * 
     * @return HTTP_OAuthProvider_Signature
     */
    public function __construct(HTTP_OAuthProvider $provider)
    {
        $this->provider = $provider;
    }


    /**
     * getSignatureBaseString
     * 
     * Return a signature base string.
     * 
     * @return String
     */
    protected function getSignatureBaseString()
    {
        $schema = 'http';
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on') {
            $schema = 'https';
        }
        $path = preg_replace('/\?.*$/', '', $_SERVER['REQUEST_URI']);
        $url = sprintf('%s://%s%s', $schema, $_SERVER['HTTP_HOST'], $path);
        $params = self::http_build_query_rfc3986($this->provider->getRequest()->getParameter());

        $base = array(
            self::urlencode_rfc3986($this->provider->getRequest()->getMethod()),
            self::urlencode_rfc3986($url),
            self::urlencode_rfc3986($params)
        );  
        return implode('&', $base);
    }


    /* static */

    /**
     * urlencode_rfc3986
     * 
     * Encodes the given string according to RFC 3986.
     * 
     * @param String $str The URL to be encoded.
     * 
     * @return String
     */
    public static function urlencode_rfc3986($str)
    {   
        $str = rawurlencode($str);
        $str = str_replace('%7E', '~', $str);
        $str = str_replace('+', ' ', $str);
        return $str;
    }   

    /**
     * http_build_query_rfc3986
     * 
     * Generate URL-encoded query string
     * 
     * @param Array $params The parameters to be URL-encoded.
     * 
     * @return String
     */
    public static function http_build_query_rfc3986($params)
    {
        $tmp = array();
        foreach ($params as $key=>$value) {
            $key = self::urlencode_rfc3986($key);
            $value = self::urlencode_rfc3986($value);
            $tmp[] = sprintf('%s=%s', $key, $value);
        }
        return implode('&', $tmp);
    }
}
