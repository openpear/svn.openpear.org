<?php

class HTTP_OAuthProvider_Consumer
{
	protected $_row = null;

	public function __construct(array $row=array())
	{
		$this->_row = $row;
	}

	public function getParam($key)
	{
		if (isset($this->_row[$key])) {
			return $this->_row[$key];
		}
		return null;
	}

	public function getKey()
	{
		return $this->getParam('key');
	}

	public function getSecret()
	{
		return $this->getParam('secret');
	}

	public function getPublicKey()
	{
		return $this->getParam('publickey');
	}
}
