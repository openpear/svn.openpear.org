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

/**
 * HTTP_OAuthProvider_RequestTest
 *
 * @category HTTP
 * @package  OAuthProvider
 * @author   Tetsuya Yoshida <tetu@eth0.jp>
 * @license  http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version  1.1.0
 * @link     http://openpear.org/package/HTTP_OAuthProvider
 */
class HTTP_OAuthProvider_RequestTest extends PHPUnit_Framework_TestCase
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
        $provider = new HTTP_OAuthProvider_Mock_HMAC_SHA1();
        $this->request = $provider->getRequest();
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
        $this->assertType('HTTP_OAuthProvider_Request', $this->request);

    }

    /** 
     * testGetMethod
     * 
     * Check a HTTP method.
     *
     * @return void
     *
     * @test
     */
    public function testGetMethod()
    {
        $this->assertEquals('GET', $this->request->getMethod());
    }

    /** 
     * testGetHeader
     * 
     * Check a HTTP header.
     *
     * @return void
     *
     * @test
     */
    public function testGetHeader()
    {
        $this->assertEquals('example.com', $this->request->getHeader('Host'));
        $this->assertNull($this->request->getHeader('Authorization'));
    }

    /** 
     * testGetSignature
     * 
     * Check an OAuth signature.
     *
     * @return void
     *
     * @test
     */
    public function testGetSignature()
    {
        $valid = 'cJadffbdVWLPv+t7cxlbgAB4HlM=';
        $this->assertEquals($valid, $this->request->getSignature());
    }

    /** 
     * testGetBody
     * 
     * Check a HTTP request body.
     *
     * @return void
     *
     * @test
     */
    public function testGetBody()
    {
        $this->assertEquals('', $this->request->getBody());
    }

    /** 
     * testBodyHash
     * 
     * Check an OAuth body hash.
     *
     * @return void
     *
     * @test
     */
    public function testBodyHash()
    {
        $valid = '2jmj7l5rSw0yVb/vlWAYkK/YBwk=';
        $this->assertEquals($valid, $this->request->getBodyHash());
    }

    /** 
     * testCheckParameters
     * 
     * Check necessary parameters.
     *
     * @return void
     *
     * @test
     */
    public function testCheckParameters()
    {
        // true test
        $params1 = array(
            'oauth_consumer_key',
            'oauth_nonce',
            'oauth_signature',
            'oauth_signature_method',
            'oauth_timestamp',
            'oauth_version'
        );
        $result1 = false;
        try {
            $result1 = $this->request->checkParameters($params1);
        } catch (Exception $e) {
        }
        $this->assertTrue($result1);

        // false test
        $params2 = array(
            'oauth_token',
            'oauth_verifier'
        );
        $result2 = false;
        try {
            $result2 = $this->request->checkParameters($params2);
        } catch (Exception $e) {
        }
        $this->assertFalse($result2);
    }

    /** 
     * testCheckBodyHash
     * 
     * Check a requested OAuth body hash.
     *
     * @return void
     *
     * @test
     */
    public function testCheckBodyHash()
    {
        $result = false;
        try {
            $result = $this->request->checkBodyHash();
        } catch (Exception $e) {
        }
        $this->assertTrue($result);
    }

    /** 
     * testCheckTimestamp
     * 
     * Check a requested OAuth timestamp.
     *
     * @return void
     *
     * @test
     */
    public function testCheckTimestamp()
    {
        // true test
        $valid_timestamp_past = time();
        $result = false;
        try {
            $result = $this->request->checkTimestamp($valid_timestamp_past, 0);
        } catch (Exception $e) {
        }
        $this->assertTrue($result);

        // false test
        $valid_timestamp_past = 0;
        $result = false;
        try {
            $result = $this->request->checkTimestamp($valid_timestamp_past, 0);
        } catch (Exception $e) {
        }
        $this->assertFalse($result);
    }
}
