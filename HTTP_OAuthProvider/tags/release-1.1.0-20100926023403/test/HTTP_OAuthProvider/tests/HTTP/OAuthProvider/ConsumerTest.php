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
 * HTTP_OAuthProvider_ConsumerTest
 *
 * @category HTTP
 * @package  OAuthProvider
 * @author   Tetsuya Yoshida <tetu@eth0.jp>
 * @license  http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version  1.1.0
 * @link     http://openpear.org/package/HTTP_OAuthProvider
 */
class HTTP_OAuthProvider_ConsumerTest extends PHPUnit_Framework_TestCase
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
        $this->consumer = $provider->getConsumer();
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
        $this->assertType('HTTP_OAuthProvider_Consumer', $this->consumer);
    }

    /** 
     * testGetterMethod
     * 
     * Check a getter method.
     *
     * @return void
     *
     * @test
     */
    public function testGetterMethod()
    {
        $publickey = '-----BEGIN CERTIFICATE-----
MIIDijCCAvOgAwIBAgIJAOXBQLEpMB4rMA0GCSqGSIb3DQEBBQUAMIGLMQswCQYD
VQQGEwJKUDEOMAwGA1UECBMFVG9reW8xETAPBgNVBAcTCFNoaW5qdWt1MRAwDgYD
VQQKEwdleGFtcGxlMRAwDgYDVQQLEwdleGFtcGxlMRQwEgYDVQQDEwtleGFtcGxl
LmNvbTEfMB0GCSqGSIb3DQEJARYQcm9vdEBleGFtcGxlLmNvbTAeFw0wOTEwMTUw
ODMyNDdaFw0xOTEwMTMwODMyNDdaMIGLMQswCQYDVQQGEwJKUDEOMAwGA1UECBMF
VG9reW8xETAPBgNVBAcTCFNoaW5qdWt1MRAwDgYDVQQKEwdleGFtcGxlMRAwDgYD
VQQLEwdleGFtcGxlMRQwEgYDVQQDEwtleGFtcGxlLmNvbTEfMB0GCSqGSIb3DQEJ
ARYQcm9vdEBleGFtcGxlLmNvbTCBnzANBgkqhkiG9w0BAQEFAAOBjQAwgYkCgYEA
orhSQotOymjP+lnDqRvrlYWKzd3M8vE82U7emeS9KQPtCBoy+fXP/kMEMxG/YU+c
NAS/2BLFGN48EPM0ZAQap384nx+TNZ6sGuCJa60go8yIWff72DZjSZI6otfPjC9S
NlxOnNLNAfGWAiaCcuBP1uJVhyrs1pu7SaEXBOP4pQ0CAwEAAaOB8zCB8DAdBgNV
HQ4EFgQU3mEIdWrvKu+yuwIJD2WczQLI3j4wgcAGA1UdIwSBuDCBtYAU3mEIdWrv
Ku+yuwIJD2WczQLI3j6hgZGkgY4wgYsxCzAJBgNVBAYTAkpQMQ4wDAYDVQQIEwVU
b2t5bzERMA8GA1UEBxMIU2hpbmp1a3UxEDAOBgNVBAoTB2V4YW1wbGUxEDAOBgNV
BAsTB2V4YW1wbGUxFDASBgNVBAMTC2V4YW1wbGUuY29tMR8wHQYJKoZIhvcNAQkB
FhByb290QGV4YW1wbGUuY29tggkA5cFAsSkwHiswDAYDVR0TBAUwAwEB/zANBgkq
hkiG9w0BAQUFAAOBgQAO2ZKL0/tPhpVfbOoSXl+tlmTyyb8w7mCnjYYWwcwUAf1N
ylgYxKPrKfamjZKpeRY487VbTee1jfud709oIK5l9ghjz64kPRn/AYHTRwRkBKbb
wuBWH4L6Rw3ml0ODXW64bdTx/QsAv5M1SyCp/nl8R27dz3MX2D1Ov2o4ipTlZw==
-----END CERTIFICATE-----';
        $this->assertEquals('testuser', $this->consumer->getKey());
        $this->assertEquals('testpass', $this->consumer->getSecret());
        $this->assertEquals($publickey, $this->consumer->getPublicKey());
    }
}
