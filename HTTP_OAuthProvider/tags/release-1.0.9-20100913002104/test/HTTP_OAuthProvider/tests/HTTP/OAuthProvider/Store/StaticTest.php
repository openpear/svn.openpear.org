<?php
require_once 'PHPUnit/Framework.php';
require_once 'HTTP/OAuthProvider.php';
require_once 'HTTP/OAuthProvider/Mock/HMAC_SHA1.php';

class HTTP_OAuthProvider_Store_StaticTest extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->provider = new HTTP_OAuthProvider_Mock_HMAC_SHA1();
		$row = array(
			'type' => 'request',
			'token' => 'yQa5LVZdRnhVD83ELbxsNOsr30iQ3oFqGtXzlWa0XKpYAXHWAchkd8R5wL1YQfxeKWgr8',
			'secret' => 'Yu50YZ1SrpNy8KmV3yV1R232GSajkzboApKgP1eyWCEVpVPskGfhjvmGtgRGpDxMyh4g',
		);
		$this->store = HTTP_OAuthProvider_Store::factory('Static', $row);
	}

	public function testInstanceType()
	{
		$this->assertType('HTTP_OAuthProvider_Store', $this->store);
	}

	public function testGetterMethod()
	{
		$token = 'yQa5LVZdRnhVD83ELbxsNOsr30iQ3oFqGtXzlWa0XKpYAXHWAchkd8R5wL1YQfxeKWgr8';
		$secret  = 'Yu50YZ1SrpNy8KmV3yV1R232GSajkzboApKgP1eyWCEVpVPskGfhjvmGtgRGpDxMyh4g';
		$this->assertEquals($token, $this->store->getToken());
		$this->assertEquals($secret, $this->store->getSecret());
	}
}
