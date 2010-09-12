<?php
require_once 'PHPUnit/Framework.php';
require_once 'HTTP/OAuthProvider.php';
require_once 'HTTP/OAuthProvider/Mock/HMAC_SHA1.php';

class HTTP_OAuthProvider_SignatureTest extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->provider = new HTTP_OAuthProvider_Mock_HMAC_SHA1();
	}

	public function testUrlencode()
	{
		$valid = '_-%2B%2A%2F.%26~%40%5B%5D%3C%3E%28%29%21%3F%20';
		$input = $this->provider->getRequest()->getParameter('testparam');
		$output = HTTP_OAuthProvider_Signature::urlencode_rfc3986($input);
		$this->assertEquals($valid, $output);
	}

	public function testHttpBuildQuery()
	{
		$valid = 'oauth_consumer_key=testuser&oauth_nonce=fcaa5f84f9a15c79248ea66159d1fa72&oauth_signature_method=HMAC-SHA1&oauth_timestamp=1284303122&oauth_version=1.0&testparam=_-%2B%2A%2F.%26~%40%5B%5D%3C%3E%28%29%21%3F%20';
		$params = $this->provider->getRequest()->getParameter();
		ksort($params);
		$output = HTTP_OAuthProvider_Signature::http_build_query_rfc3986($params);
		$this->assertEquals($valid, $output);
	}
}
