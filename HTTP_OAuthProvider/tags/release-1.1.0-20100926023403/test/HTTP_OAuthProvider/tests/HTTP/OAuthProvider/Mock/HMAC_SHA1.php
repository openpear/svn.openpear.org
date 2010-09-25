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
require_once 'HTTP/OAuthProvider.php';
require_once 'HTTP/OAuthProvider/Mock/Request.php';

/**
 * HTTP_OAuthProvider_Mock_HMAC_SHA1
 *
 * @category HTTP
 * @package  OAuthProvider
 * @author   Tetsuya Yoshida <tetu@eth0.jp>
 * @license  http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version  1.1.0
 * @link     http://openpear.org/package/HTTP_OAuthProvider
 */
class HTTP_OAuthProvider_Mock_HMAC_SHA1 extends HTTP_OAuthProvider
{
    /** 
     * __construct
     * 
     * Generate the HTTP_OAuthProvider_Mock_HMAC_SHA1 instance.
     * This class emulates OAuth HMAC-SHA1 request.
     * 
     * @return HTTP_OAuthProvider_Mock_HMAC_SHA1
     */
    public function __construct()
    {
        // setup oauth requeset emulated
        $_GET = array(
            'oauth_consumer_key'        => 'testuser',
            'oauth_nonce'               => 'fcaa5f84f9a15c79248ea66159d1fa72',
            'oauth_signature'           => 'cJadffbdVWLPv+t7cxlbgAB4HlM=',
            'oauth_signature_method'    => 'HMAC-SHA1',
            'oauth_timestamp'           => '1284303122',
            'oauth_version'             => '1.0',
            'testparam'                 => '_-+*/.&~@[]<>()!? '
        );
        $_POST = array();

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/rest';
        $_SERVER['HTTP_HOST'] = 'example.com';

        // set valid timestamp
        $valid_timestamp_past = time();
        $this->setValidTimestamp($valid_timestamp_past);

        // set request
        $this->request = HTTP_OAuthProvider_Mock_Request::getInstance();
    }

    /**
     * getConsumer
     * 
     * Return a HTTP_OAuthProvider_Consumer instance.
     * 
     * @return Boolean
     */
    public function getConsumer()
    {
        $row = array(
            'key' => 'testuser',
            'secret' => 'testpass',
            'publickey' => '-----BEGIN CERTIFICATE-----
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
-----END CERTIFICATE-----'
        );
        return new HTTP_OAuthProvider_Consumer($row);
    }

    /**
     * fetchConsumer
     * 
     * Return a HTTP_OAuthProvider_Consumer instance.
     * 
     * @param String $consumer_key A consumer key to fetch.
     * 
     * @return HTTP_OAuthProvider_Consumer
     */
    public function fetchConsumer($consumer_key)
    {
        return $this->getConsumer();
    }
}
