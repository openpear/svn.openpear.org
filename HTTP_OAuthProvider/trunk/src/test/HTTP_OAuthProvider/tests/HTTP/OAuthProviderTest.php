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
require_once 'PHPUnit/Framework.php';
require_once 'HTTP/OAuthProvider.php';
require_once 'HTTP/OAuthProvider/Mock/HMAC_SHA1.php';
require_once 'HTTP/OAuthProvider/Mock/RSA_SHA1.php';

/**
 * HTTP_OAuthProviderTest
 *
 * @category HTTP
 * @package  OAuthProvider
 * @author   Tetsuya Yoshida <tetu@eth0.jp>
 * @license  http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version  1.1.0
 * @link     http://openpear.org/package/HTTP_OAuthProvider
 */
class HTTP_OAuthProviderTest extends PHPUnit_Framework_TestCase
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
        $this->provider_hmac_sha1 = new HTTP_OAuthProvider_Mock_HMAC_SHA1();
        $this->provider_rsa_sha1 = new HTTP_OAuthProvider_Mock_RSA_SHA1();
    }

    /** 
     * testInstanceType
     * 
     * Check a instance type.
     *
     * @return void
     *
     * @test
     */
    public function testInstanceType()
    {
        // HMAC-SHA1
        $this->assertType('HTTP_OAuthProvider', $this->provider_hmac_sha1);

        // RSA-SHA1
        $this->assertType('HTTP_OAuthProvider', $this->provider_rsa_sha1);
    }

    /** 
     * testAuthenticate
     * 
     * Check an authentication.
     *
     * @return void
     *
     * @test
     */
    public function testAuthenticate()
    {
        // HMAC-SHA1
        $result = false;
        try {
            $result = $this->provider_hmac_sha1->authenticate();
        } catch (Exception $e) {
        }
        $this->assertTrue($result);

        // RSA-SHA1
        $result = false;
        try {
            $result = $this->provider_rsa_sha1->authenticate();
        } catch (Exception $e) {
        }
        $this->assertTrue($result);
    }
}
