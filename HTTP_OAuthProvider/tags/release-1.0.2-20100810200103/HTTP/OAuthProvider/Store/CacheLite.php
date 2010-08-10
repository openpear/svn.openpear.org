<?php
require_once('Cache/Lite.php');

class HTTP_OAuthProvider_Store_CacheLite extends HTTP_OAuthProvider_Store
{
	protected $_default = array(
		'cacheDir' => '/tmp/http_oauthprovider/',
		'lifeTime' => 3600
	);
	protected $_cache = null;

	public function __construct(array $options=array())
	{
		$options = array_merge($this->_default, $options);
		$options['cacheDir'] = rtrim($options['cacheDir'], '/').'/';
		// make cache dir
		if (!is_dir($options['cacheDir'])) {
			mkdir($options['cacheDir'], 0777, true);
		}
		// make store instance
		$this->_cache = new Cache_Lite($options);
	}

	public function get($token)
	{
		return unserialize($this->_cache->get($token));
	}

	public function save()
	{
		return $this->_cache->save(serialize($this->_row), $this->getToken());
	}

	public function remove()
	{
		return $this->_cache->remove($this->getToken());
	}
}
