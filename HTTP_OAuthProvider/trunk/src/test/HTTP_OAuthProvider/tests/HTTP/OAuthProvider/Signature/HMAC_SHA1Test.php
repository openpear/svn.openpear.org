<?php
require_once 'PHPUnit/Framework.php';
require_once 'HTTP/OAuthProvider.php';
require_once 'HTTP/OAuthProvider/Mock/HMAC_SHA1.php';

class HTTP_OAuthProvider_Signature_HMAC_SHA1Test extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->provider = new HTTP_OAuthProvider_Mock_HMAC_SHA1();
		$this->signature = HTTP_OAuthProvider_Signature::factory($this->provider);
	}

	public function testInstanceType()
	{
		$this->assertType('HTTP_OAuthProvider_Signature', $this->signature);
	}

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
