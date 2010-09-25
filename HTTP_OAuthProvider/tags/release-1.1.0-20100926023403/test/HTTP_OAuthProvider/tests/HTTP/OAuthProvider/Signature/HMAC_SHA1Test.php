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
 * HTTP_OAuthProvider_Signature_HMAC_SHA1Test
 *
 * @category HTTP
 * @package  OAuthProvider
 * @author   Tetsuya Yoshida <tetu@eth0.jp>
 * @license  http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version  1.1.0
 * @link     http://openpear.org/package/HTTP_OAuthProvider
 */
class HTTP_OAuthProvider_Signature_HMAC_SHA1Test extends PHPUnit_Framework_TestCase
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
        $this->signature = HTTP_OAuthProvider_Signature::factory($this->provider);
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
        $this->assertType('HTTP_OAuthProvider_Signature', $this->signature);
    }

    /** 
     * testCheckSignature
     * 
     * Check a requested OAuth signature.
     *
     * @return void
     *
     * @test
     */
    public function testCheckSignature()
    {
        $result = false;
        try {
            $result = $this->signature->checkSignature();
        } catch (Exception $e) {
        }
        $this->assertTrue($result);
    }
}
