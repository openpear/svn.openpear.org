<?php
class HTTP_OAuthProvider_Store_Memcached extends HTTP_OAuthProvider_Store
{
	protected $_options = array(
		'host' => '127.0.0.1',
		'port' => 11211,
		'prefix' => 'http_oauthprovider_',
		'explain' => 3600
	);
	protected $_mem = null;

	public function __construct(array $options=array())
	{
		$this->_options = array_merge($this->_options, $options);
		$host = $this->_options['host'];
		$port = $this->_options['port'];
		// make store instance
		$this->_mem = new Memcached();
		$connected = $this->_mem->addServer($host, $port);
		if (!$connected) {
			$message = sprintf("Can't connect to %s:%s, Connection refused", $host, $port);
			throw new HTTP_OAuthProvider_Store_Exception($message, 500);
		}
	}

	public function get($token)
	{
		$key = $this->_options['prefix'] . $token;
		return $this->_mem->get($key);
	}

	public function save()
	{
		$key = $this->_options['prefix'] . $this->getToken();
		return $this->_mem->set($key, $this->_row, $this->_options['explain']);
	}

	public function remove()
	{
		$key = $this->_options['prefix'] . $this->getToken();
		return $this->_mem->delete($key);
	}
}
