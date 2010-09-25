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
require_once 'PHPUnit/Framework.php';
require_once 'HTTP/OAuthProvider.php';
require_once 'HTTP/OAuthProvider/Mock/HMAC_SHA1.php';

/**
 * HTTP_OAuthProvider_SignatureTest
 *
 * @category HTTP
 * @package  OAuthProvider
 * @author   Tetsuya Yoshida <tetu@eth0.jp>
 * @license  http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version  1.1.0
 * @link     http://openpear.org/package/HTTP_OAuthProvider
 */
class HTTP_OAuthProvider_SignatureTest extends PHPUnit_Framework_TestCase
{
    /** 
     * setUp
     * 
     * Set up a mock object.
     *
     * @return void
     */
    public function setUp()
    {
        $this->provider = new HTTP_OAuthProvider_Mock_HMAC_SHA1();
    }

    /** 
     * testUrlencode
     * 
     * Check urlencode_rfc3986.
     *
     * @return void
     *
     * @test
     */
    public function testUrlencode()
    {
        $valid = '_-%2B%2A%2F.%26~%40%5B%5D%3C%3E%28%29%21%3F%20';
        $input = $this->provider->getRequest()->getParameter('testparam');
        $output = HTTP_OAuthProvider_Signature::urlencode_rfc3986($input);
        $this->assertEquals($valid, $output);
    }

    /** 
     * testHttpBuildQuery
     * 
     * Check http_build_query_rfc3986.
     *
     * @return void
     *
     * @test
     */
    public function testHttpBuildQuery()
    {
        $valid = 'oauth_consumer_key=testuser&oauth_nonce=fcaa5f84f9a15c79248ea66159d1fa72&oauth_signature_method=HMAC-SHA1&oauth_timestamp=1284303122&oauth_version=1.0&testparam=_-%2B%2A%2F.%26~%40%5B%5D%3C%3E%28%29%21%3F%20';
        $params = $this->provider->getRequest()->getParameter();
        $output = HTTP_OAuthProvider_Signature::http_build_query_rfc3986($params);
        $this->assertEquals($valid, $output);
    }
}
