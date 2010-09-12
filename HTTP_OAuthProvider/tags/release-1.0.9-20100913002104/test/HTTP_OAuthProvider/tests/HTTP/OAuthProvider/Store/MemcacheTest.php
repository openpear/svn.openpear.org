<?php
require_once 'PHPUnit/Framework.php';
require_once 'HTTP/OAuthProvider.php';
require_once 'HTTP/OAuthProvider/Mock/HMAC_SHA1.php';

class HTTP_OAuthProvider_Store_MemcacheTest extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->provider = new HTTP_OAuthProvider_Mock_HMAC_SHA1();
		$this->store = HTTP_OAuthProvider_Store::factory('Memcache');
	}

	public function testInstanceType()
	{
		$this->assertType('HTTP_OAuthProvider_Store', $this->store);
	}

	public function testIssueTokenProcess()
	{
		// issue request token
		$this->store->issueRequestToken($this->provider);
		$this->assertEquals('request', $this->store->getType());
		$this->assertEquals('testuser', $this->store->getConsumerKey());
		$this->assertNotNull($this->store->getTimestamp());
		$this->assertNotNull($this->store->getToken());
		$this->assertNotNull($this->store->getSecret());
		$this->assertTrue($this->store->save());
		$token = $this->store->getToken();

		// authorize request token
		$user_id = '123456789';
		$this->store->loadToken($this->provider, $token);
		$this->store->authorizeToken($user_id);
		$this->assertEquals('authorize', $this->store->getType());
		$this->assertEquals($token, $this->store->getToken());
		$this->assertEquals($user_id, $this->store->getUserID());
		$this->assertTrue($this->store->save());

		// exchange access token
		$this->store->loadToken($this->provider, $token);
		$this->store->exchangeAccessToken();
		$this->assertEquals('access', $this->store->getType());
		$this->assertNotEquals($token, $this->store->getToken());
		$this->assertTrue($this->store->save());
	}
}
