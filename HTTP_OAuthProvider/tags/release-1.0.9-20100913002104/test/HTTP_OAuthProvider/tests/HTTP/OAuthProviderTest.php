<?php
require_once 'PHPUnit/Framework.php';
require_once 'HTTP/OAuthProvider.php';
require_once 'HTTP/OAuthProvider/Mock/HMAC_SHA1.php';

class HTTP_OAuthProviderTest extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->provider = new HTTP_OAuthProvider_Mock_HMAC_SHA1();
	}

	public function testInstanceType()
	{
		$this->assertType('HTTP_OAuthProvider', $this->provider);
	}

	public function testAuthenticate()
	{
		$result = false;
		try {
			$result = $this->provider->authenticate();
		} catch (Exception $e) {
		}
		$this->assertTrue($result);
	}
}
