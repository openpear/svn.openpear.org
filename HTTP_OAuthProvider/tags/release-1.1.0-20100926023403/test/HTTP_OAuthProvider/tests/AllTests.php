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
require_once 'HTTP/OAuthProviderTest.php';
require_once 'HTTP/OAuthProvider/ConsumerTest.php';
require_once 'HTTP/OAuthProvider/RequestTest.php';
require_once 'HTTP/OAuthProvider/SignatureTest.php';
require_once 'HTTP/OAuthProvider/Signature/HMAC_SHA1Test.php';
require_once 'HTTP/OAuthProvider/Signature/RSA_SHA1Test.php';
require_once 'HTTP/OAuthProvider/Store/CacheLiteTest.php';
require_once 'HTTP/OAuthProvider/Store/MemcacheTest.php';
require_once 'HTTP/OAuthProvider/Store/MemcachedTest.php';
require_once 'HTTP/OAuthProvider/Store/StaticTest.php';

/**
 * AllTests
 *
 * @category HTTP
 * @package  OAuthProvider
 * @author   Tetsuya Yoshida <tetu@eth0.jp>
 * @license  http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version  1.1.0
 * @link     http://openpear.org/package/HTTP_OAuthProvider
 */
class AllTests
{
    /** 
     * suite
     * 
     * Returns the whole suite for HTTP_OAuthProvider.
     *
     * @return PHPUnit_Framework_TestSuite Unit test suite
     */
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite();
        $suite->addTestSuite('HTTP_OAuthProviderTest');
        $suite->addTestSuite('HTTP_OAuthProvider_ConsumerTest');
        $suite->addTestSuite('HTTP_OAuthProvider_RequestTest');
        $suite->addTestSuite('HTTP_OAuthProvider_SignatureTest');
        $suite->addTestSuite('HTTP_OAuthProvider_Signature_HMAC_SHA1Test');
        $suite->addTestSuite('HTTP_OAuthProvider_Signature_RSA_SHA1Test');
        $suite->addTestSuite('HTTP_OAuthProvider_Store_CacheLiteTest');
        $suite->addTestSuite('HTTP_OAuthProvider_Store_MemcacheTest');
        $suite->addTestSuite('HTTP_OAuthProvider_Store_MemcachedTest');
        $suite->addTestSuite('HTTP_OAuthProvider_Store_StaticTest');
        return $suite;
    }
}
