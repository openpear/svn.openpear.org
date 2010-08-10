<?php
require_once('Cache/Lite.php');

class HTTP_OAuthProvider_Store_CacheLite extends HTTP_OAuthProvider_Store
{
	protected $_options = array(
		'cacheDir' => '/tmp/http_oauthprovider/',
		'lifeTime' => 3600
	);
	protected $_cache = null;

	public function __construct(array $options=array())
	{
		$this->_options = array_merge($this->_options, $options);
		$this->_options['cacheDir'] = rtrim($this->_options['cacheDir'], '/').'/';
		$dir = $this->_options['cacheDir'];
		// make cache dir
		if (!is_dir($dir)) {
			$maked = @mkdir($dir, 0777, true);
			if (!$maked) {
				$message = sprintf("Can's make directory: %s", $dir);
				throw new HTTP_OAuthProvider_Store_Exception($message, 500);
			}
		}
		// make store instance
		$this->_cache = new Cache_Lite($this->_options);
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
