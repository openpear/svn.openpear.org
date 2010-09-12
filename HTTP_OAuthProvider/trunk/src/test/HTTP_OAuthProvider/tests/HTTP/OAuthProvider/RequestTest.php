<?php
require_once 'PHPUnit/Framework.php';
require_once 'HTTP/OAuthProvider.php';
require_once 'HTTP/OAuthProvider/Mock/HMAC_SHA1.php';

class HTTP_OAuthProvider_RequestTest extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$provider = new HTTP_OAuthProvider_Mock_HMAC_SHA1();
		$this->request = $provider->getRequest();
	}

	public function testInstanceType()
	{
		$this->assertType('HTTP_OAuthProvider_Request', $this->request);

	}

	public function testGetMethod()
	{
		$this->assertEquals('GET', $this->request->getMethod());
	}

	public function testGetHeader()
	{
		$this->assertEquals('example.com', $this->request->getHeader('Host'));
		$this->assertNull($this->request->getHeader('Authorization'));
	}

	public function testGetSignature()
	{
		$valid = 'cJadffbdVWLPv+t7cxlbgAB4HlM=';
		$this->assertEquals($valid, $this->request->getSignature());
	}

	public function testGetBody()
	{
		$this->assertEquals('', $this->request->getBody());
	}

	public function testBodyHash()
	{
		$valid = '2jmj7l5rSw0yVb/vlWAYkK/YBwk=';
		$this->assertEquals($valid, $this->request->getBodyHash());
	}

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

	public function testCheckBodyHash()
	{
		$result = false;
		try {
			$result = $this->request->checkBodyHash();
		} catch (Exception $e) {
		}
		$this->assertTrue($result);
	}

	public function testCheckTimestamp()
	{
		// true test
		$valid_timestamp_past1 = time();
		$result1 = false;
		try {
			$result1 = $this->request->checkTimestamp($valid_timestamp_past1, 0);
		} catch (Exception $e) {
		}
		$this->assertTrue($result1);

		// false test
		$valid_timestamp_past2 = 0;
		$result2 = false;
		try {
			$result2 = $this->request->checkTimestamp($valid_timestamp_past2, 0);
		} catch (Exception $e) {
		}
		$this->assertFalse($result2);
	}
}
