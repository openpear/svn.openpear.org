<?php

require_once dirname(__FILE__) . '/../../../src/HTTP/OAuthConsumer.php';
require_once 'HTTP/Request2/Adapter/Mock.php';

/**
 * Test class for HTTP_OAuthConsumer.
 * Generated by PHPUnit on 2011-07-15 at 00:27:33.
 */
class HTTP_OAuthConsumerTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @var HTTP_OAuthConsumer
	 */
	protected $object;
	
	/**
	 *
	 * @var HTTP_Request2_Adapter_Mock
	 */
	protected $mock;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp()
	{
		$this->object = HTTP_OAuthConsumer::factory();
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown()
	{
		
	}

	/**
	 * @todo Implement testFactory().
	 */
	public function testFactory()
	{
		$this->assertType('HTTP_OAuthConsumer_HMAC_SHA1', $this->object, 'Default sign method should be HMAC SHA1.');
		$this->assertType('HTTP_OAuthConsumer_HMAC_SHA1', HTTP_OAuthConsumer::factory('HMAC-SHA1'));
		$this->assertType('HTTP_OAuthConsumer_RSA_SHA1', HTTP_OAuthConsumer::factory('RSA-SHA1'));
	}

	public function testHmacSha1Get()
	{
		$oauth = $this->object;
		$oauth->setURL('http://example.com/?format=json');
		$oauth->setTimestamp(1310679194);
		$oauth->setNonce('8de41c132c43bc81');

		$oauth->addGetParameter('foo', 'bar');
		$oauth->setConsumer('key', 'secret');
		$oauth->setToken('accesskey', 'accesssecret');
		$response = $this->sendRequest(200);
		
		$header = $oauth->getHeaders();
		$this->assertEquals('200', $response->getStatus());
		$this->assertEquals('OAuth oauth_consumer_key="key", oauth_nonce="8de41c132c43bc81", oauth_signature="p1HdbcLPhGzCiDYCP6Gd2ih9uvY%3D", oauth_signature_method="HMAC-SHA1", oauth_timestamp="1310679194", oauth_token="accesskey", oauth_version="1.0"', $header['authorization']);
	}

	public function testSetRealm()
	{
		$oauth = $this->object;
		$oauth->setURL('http://example.com/?format=json');
		$oauth->setTimestamp(1310679194);
		$oauth->setNonce('8de41c132c43bc81');

		$oauth->addGetParameter('foo', 'bar');
		$oauth->setConsumer('key', 'secret');
		$oauth->setToken('accesskey', 'accesssecret');
		$oauth->setRealm('test-realm');
		$response = $this->sendRequest();
		
		$header = $oauth->getHeaders();
		$this->assertEquals('200', $response->getStatus());
		$this->assertEquals('OAuth realm="test-realm", oauth_consumer_key="key", oauth_nonce="8de41c132c43bc81", oauth_signature="p1HdbcLPhGzCiDYCP6Gd2ih9uvY%3D", oauth_signature_method="HMAC-SHA1", oauth_timestamp="1310679194", oauth_token="accesskey", oauth_version="1.0"', $header['authorization']);
	}
	
	public function testExtraOAuthParam()
	{
		$oauth = $this->object;
		$oauth->setURL('http://example.com/?format=json');
		$oauth->setTimestamp(1310679194);
		$oauth->setNonce('8de41c132c43bc81');

		$oauth->addOAuthParameter('foo', 'bar');
		$oauth->setConsumer('key', 'secret');
		$oauth->setToken('accesskey', 'accesssecret');
		$response = $this->sendRequest(200);
		
		$header = $oauth->getHeaders();
		$this->assertEquals('200', $response->getStatus());
		$this->assertEquals('OAuth foo="bar", oauth_consumer_key="key", oauth_nonce="8de41c132c43bc81", oauth_signature="p1HdbcLPhGzCiDYCP6Gd2ih9uvY%3D", oauth_signature_method="HMAC-SHA1", oauth_timestamp="1310679194", oauth_token="accesskey", oauth_version="1.0"', $header['authorization']);

	}
	
	/**
	 *
	 * @expectedException HTTP_OAuthConsumer_Exception
	 * @dataProvider httpNot200StatusCodeProvider 
	 */
	public function testResponseStatusCheck($status)
	{
		$oauth = $this->object;
		$oauth->setURL('http://example.com/?format=json');
		$oauth->setTimestamp(1310679194);
		$oauth->setNonce('8de41c132c43bc81');

		$oauth->setConsumer('key', 'secret');
		$oauth->setToken('accesskey', 'accesssecret');
		$response = $this->sendRequest($status);
	}

	/**
	 *
	 * @dataProvider httpNot200StatusCodeProvider 
	 */
	public function testDisableResponseStatusCheck($status)
	{
		$oauth = $this->object;
		$oauth->setURL('http://example.com/?format=json');
		$oauth->setTimestamp(1310679194);
		$oauth->setNonce('8de41c132c43bc81');

		$oauth->setConsumer('key', 'secret');
		$oauth->setToken('accesskey', 'accesssecret');
		
		$oauth->enableResponseStatusCheck(false);
		
		$response = $this->sendRequest($status);
		
		$this->assertEquals($status, $response->getStatus());
	}
	
	public function httpNot200StatusCodeProvider()
	{
		return array(
			array(201),
			array(301),
			array(401),
			array(503),
		);
	}
	
	public function testSetNonceAndSetTimestamp()
	{
		$oauth = $this->object;
		
		$oauth->setTimestamp(time());
		$oauth->setNonce('v0GLNzG0kAn');
		
		$oauth->setURL('http://example.com/');
		$oauth->setConsumer('testuser', 'testpass');
		$this->sendRequest(200);
		
		$header1 = $oauth->getHeaders();
		
		sleep(1);
		$this->sendRequest(200);
		
		$this->assertEquals($header1, $oauth->getHeaders());
	}
	
	public function testNonce()
	{
		$oauth = $this->object;
		
		// keep timestamp
		$oauth->setTimestamp(time());
		
		$oauth->setURL('http://example.com/');
		$oauth->setConsumer('testuser', 'testpass');
		$this->sendRequest(200);
		
		$header1 = $oauth->getHeaders();
		
		$this->sendRequest(200);
		
		$this->assertNotEquals($header1, $oauth->getHeaders());
	}
	
	public function testTimestamp()
	{
		$oauth = $this->object;
		
		// keep nonce
		$oauth->setNonce('v0GLNzG0kAn');
		
		$oauth->setURL('http://example.com/');
		$oauth->setConsumer('testuser', 'testpass');
		$this->sendRequest(200);
		
		$header1 = $oauth->getHeaders();
		
		sleep(1);
		
		$this->sendRequest(200);
		
		$this->assertNotEquals($header1, $oauth->getHeaders());
	}
	
	protected function sendRequest($status = 200)
	{
		$mock = new HTTP_Request2_Adapter_Mock();
		$mock->addResponse(<<<EOT
HTTP/1.1 $status OK
Content-Type: application/json

{'entry':['hoge']}
EOT
				);
		
		$this->object->setAdapter($mock);
		return $this->object->send();
	}
}

?>
