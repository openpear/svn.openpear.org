<?php
require_once('HTTP/OAuthConsumer.php');

class HTTP_OAuthConsumer_HMAC_SHA1 extends HTTP_OAuthConsumer
{
	public function getSignatureMethod()
	{
		return 'HMAC-SHA1';
	}

	protected function _makeSignature()
	{
		$base_string = $this->_makeSignatureBaseString();
		$key = $this->_consumer_secret.'&'.$this->_oauth_token_secret;
		return base64_encode(hash_hmac('sha1', $base_string, $key, true));
	}
}
