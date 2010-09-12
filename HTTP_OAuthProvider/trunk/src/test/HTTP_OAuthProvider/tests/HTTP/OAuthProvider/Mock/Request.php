<?php
require_once 'HTTP/OAuthProvider.php';

class HTTP_OAuthProvider_Mock_Request extends HTTP_OAuthProvider_Request
{
	public static function getInstance()
	{
		return new HTTP_OAuthProvider_Request();
	}
}
