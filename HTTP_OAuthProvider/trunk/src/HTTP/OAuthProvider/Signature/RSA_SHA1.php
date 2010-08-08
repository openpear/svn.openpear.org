<?php

class HTTP_OAuthProvider_Signature_RSA_SHA1 extends HTTP_OAuthProvider_Signature
{
    /**
     * _checkPublicKey
     * 
     * Finds whether a $oauth_signature is a valid string
     * 
     * @return String
     */
	public function checkSignature()
    {
		$public_key = $this->_provider->getConsumer()->getPublicKey();
		$base_string = $this->_getSignatureBaseString();
		$signature = base64_decode($this->_provider->getRequest()->getSignature());
        if ($public_key) {
            $publickeyid = openssl_get_publickey($public_key);
            $ok = openssl_verify($base_string, $signature, $publickeyid);
            openssl_free_key($publickeyid);
            if ($ok) {
                return true;
            }
        }
        throw new HTTP_OAuthProvider_Exception('401 Unauthorized', 401);
    }

}
