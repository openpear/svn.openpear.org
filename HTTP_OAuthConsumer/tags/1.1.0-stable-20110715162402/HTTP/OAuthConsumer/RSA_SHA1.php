<?php
require_once('HTTP/OAuthConsumer.php');

class HTTP_OAuthConsumer_RSA_SHA1 extends HTTP_OAuthConsumer
{
	public function setConsumer($consumer_key, $privatekey, $is_file=false)
	{
		if ($is_file) {
			if (!is_file($privatekey)) {
				throw new HTTP_OAuthConsumer_Exception('No such private key file');
			}
			$privatekey = file_get_contents($privatekey);
		}
		$this->_consumer_key = $consumer_key;
		$this->_consumer_secret = $privatekey;
	}

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
