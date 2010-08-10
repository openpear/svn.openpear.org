<?php

class HTTP_OAuthProvider_Signature_HMAC_SHA1 extends HTTP_OAuthProvider_Signature
{
	public function checkSignature()
    {
        $signature = $this->_getSignature();
		$req_signature = $this->_provider->getRequest()->getSignature();
        if ($signature==$req_signature) {
            return true;
        }
        throw new HTTP_OAuthProvider_Exception('401 Unauthorized', 401);
    }

    /**
     * _getSignature
     * 
     * Return a signature
     * 
     * @return String
     */
    protected function _getSignature()
    {
		// signature base string
		$base_string = $this->_getSignatureBaseString();

		// consumer secret
		$secret = $this->_provider->getConsumer()->getSecret();

		// token secret
		$token_secret = '';
		$token = $this->_provider->getRequest()->getParameter('oauth_token');
		if ($token) {
			$store = $this->_provider->getStore();
			try {
				$store->loadToken($this->_provider);
			} catch(Exception $e) {
			}
			$token_secret = $store->getSecret();
		}

		// signature
		$key = sprintf('%s&%s', $secret, $token_secret);
        return base64_encode(hash_hmac('sha1', $base_string, $key, true));
    }
}
