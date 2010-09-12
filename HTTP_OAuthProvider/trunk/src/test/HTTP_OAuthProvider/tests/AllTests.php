<?php
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

class AllTests
{
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
