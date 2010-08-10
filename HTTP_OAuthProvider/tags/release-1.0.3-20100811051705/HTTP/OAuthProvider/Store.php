<?php
abstract class HTTP_OAuthProvider_Store
{
	protected $_row = null;

	public static function factory($driver='CacheLite', array $options=array())
	{
		$driver = str_replace('-', '_', $driver);

		$file = sprintf('%s/Store/%s.php', dirname(__FILE__), $driver);
		$class = sprintf('HTTP_OAuthProvider_Store_%s', $driver);
		if (!is_file($file)) {
			throw new HTTP_OAuthProvider_Store_Exception('Store driver is not found', 500);
		}
		require_once($file);
		if (!class_exists($class) || !is_subclass_of($class, 'HTTP_OAuthProvider_Store')) {
			throw new HTTP_OAuthProvider_Store_Exception('Store driver is not found', 500);
		}
		return new $class($options);
	}


	/* initialize */

	public function issueRequestToken(HTTP_OAuthProvider $provider)
	{
        $consumer = $provider->getConsumer();
        $request = $provider->getRequest();
        $this->_row = array(
            'type'			=> 'request',
            'consumer_key'	=> $consumer->getKey(),
            'callback'		=> $request->getParameter('oauth_callback'),
            'timestamp'		=> $request->getParameter('oauth_timestamp'),
			'token'			=> self::makeToken(),
			'secret'		=> self::makeSecret()
        );
	}

	public function loadToken(HTTP_OAuthProvider $provider)
	{
        $consumer = $provider->getConsumer();
        $request = $provider->getRequest();
		$token = $request->getParameter('oauth_token');
		$this->_row = $this->get($token);
		if ($this->_row) {
			return $this->_row['type'];
		}
		throw new HTTP_OAuthProvider_Exception('404 Not found token in store', 404);
	}


	/* update */

	public function authorizeToken($user_id)
	{
		if (isset($this->_row['type']) && $this->_row['type']=='request') {
			$this->_row['type'] = 'authorize';
			$this->_row['verifier'] = self::makeVerifier();
			$this->_row['user_id'] = $user_id;
			return;
		}
		throw new HTTP_OAuthProvider_Exception('404 Not found request token in store', 404);
	}

	public function exchangeAccessToken()
	{
		if (isset($this->_row['type']) && $this->_row['type']=='authorize') {
			$this->_row['type'] = 'access';
			$this->_row['token'] = self::makeToken();
			return;
		}
		throw new HTTP_OAuthProvider_Exception('404 Not found authorize token in store', 404);
	}


	/* Get method */

	public function getParam($key)
	{
		if (isset($this->_row[$key])) {
			return $this->_row[$key];
		}
		return null;
	}

	public function getType()
	{
		return $this->getParam('type');
	}

	public function getConsumerKey()
	{
		return $this->getParam('consumer_key');
	}

	public function getCallback()
	{
		return $this->getParam('callback');
	}

	public function getTimestamp()
	{
		return $this->getParam('timestamp');
	}

    public function getToken()
    {
		return $this->getParam('token');
    }

    public function getSecret()
    {
		return $this->getParam('secret');
    }

    public function getVerifier()
    {
		return $this->getParam('verifier');
    }

	public function getUserID()
	{
		return $this->getParam('user_id');
	}


	/* abstract */

	abstract public function __construct(array $options=array());
	abstract public function get($token);
	abstract public function save();
	abstract public function remove();


	/* utils */

    /**
     * makeToken
     * 
     * create random string
     * 
     * @return String
     */
    public static function makeToken()
    {
        $token = '';
        for ($i=0; $i<3; $i++) {
            $m = mt_rand(0, 1) ? 'sha1' : 'md5';
            $token .= $m($token.microtime().mt_rand(), 1);
        }
        $token = base64_encode($token);
        $token = str_replace(array('=', '/', '+'), array('', '', ''), $token);
        return $token;
    }

    /**
     * makeSecret
     * 
     * create random string
     * 
     * @return String
     */
    public static function makeSecret()
    {
        return self::makeToken();
    }

    /**
     * makeVerifier
     * 
     * create random string
     * 
     * @return String
     */
    public static function makeVerifier()
    {
        return self::makeToken();
    }
}
