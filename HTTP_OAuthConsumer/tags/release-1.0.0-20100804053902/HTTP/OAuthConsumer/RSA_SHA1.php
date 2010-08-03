<?php
require_once('HTTP/OAuthConsumer.php');

class HTTP_OAuthConsumer_RSA_SHA1 extends HTTP_OAuthConsumer
{
	public function getSignatureMethod()
	{
		return 'RSA-SHA1';
	}

	protected function _makeSignature()
	{
		$base_string = $this->_makeSignatureBaseString();
		$privatekeyid = openssl_get_privatekey($this->_consumer_secret);
		$ok = openssl_sign($base_string, $signature, $privatekeyid);
		openssl_free_key($privatekeyid);
		return base64_encode($signature);
	}
}
