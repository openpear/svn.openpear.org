<?php
require_once('HTTP/Request2.php');
require_once('HTTP/OAuthConsumer/Exception.php');

abstract class HTTP_OAuthConsumer extends HTTP_Request2
{
	// oauth consumer
	protected $_consumer_key = null;
	protected $_consumer_secret = null;

	// oauth token
	protected $_oauth_token = '';
	protected $_oauth_token_secret = '';
	protected $_oauth_verifier = '';
	protected $_oauth_callback = '';

	// oauth parameters
	protected $_oauth = array();
	protected $_realm = '';

	// http options
	protected $getParams = array();

	// last response
	protected $_last_response = null;


	/* construct */

	public function __construct($url = null, $method = self::METHOD_GET, array $config = array())
	{
		parent::__construct($url, $method, $config);
		$agent = 'HTTP_OAuthConsumer/1.0.5 (http://openpear.org/package/HTTP_OAuthConsumer) PHP/'.phpversion();
		$this->setHeader('user-agent', $agent);
	}


	/* factory */

	public static function factory($sig_method='HMAC-SHA1')
	{
		$sig_method = str_replace('-', '_', $sig_method);

		$file = sprintf('%s/OAuthConsumer/%s.php', dirname(__FILE__), $sig_method);
		$class = sprintf('HTTP_OAuthConsumer_%s', $sig_method);
		if (!is_file($file)) {
			throw new HTTP_OAuthConsumer_Exception('No such file');
		}
		require_once($file);
		if (!class_exists($class) || !is_subclass_of($class, 'HTTP_OAuthConsumer')) {
			throw new HTTP_OAuthConsumer_Exception('No such signature class');
		}
		return new $class();
	}


	/* OAuth options setter */

	public function setConsumer($consumer_key, $consumer_secret)
	{
		$this->_consumer_key = $consumer_key;
		$this->_consumer_secret = $consumer_secret;
	}

	public function setToken($token, $token_secret)
	{
		$this->_oauth_token = $token;
		$this->_oauth_token_secret = $token_secret;
	}

	public function setRealm($realm)
	{
		$this->_realm = $realm;
	}


	/* 3Legged OAuth */

	public function getRequestToken($callback)
	{
		$this->_oauth_callback = $callback;
		$res = $this->send();

		// check response body
		parse_str($res->getBody(), $request);
		if (!isset($request['oauth_token'], $request['oauth_token_secret'])) {
			$message = sprintf('Response body error: %s', $res->getBody());
			throw new HTTP_OAuthConsumer_Exception($message);
		}

		// set token
		$this->setToken($request['oauth_token'], $request['oauth_token_secret']);

		return $request;
	}	

	public function getAuthorizeURL($authorize_url)
	{
		// check oauth token
		if (!strlen($this->_oauth_token)) {
			throw new HTTP_OAuthConsumer_Exception('oauth token is not set');
		}

		// default params
		$params = array(
			'oauth_token' => $this->_oauth_token
		);

		// parse url
		if (strpos($authorize_url, '?')) {
			list($authorize_url, $params_str) = explode('?', $authorize_url, 2);
			parse_str($params_str, $params_tmp);
			$params = array_merge($params_tmp, $params);
		}

		return sprintf('%s?%s', $authorize_url, http_build_query($params));
	}

	public function getAccessToken($verifier)
	{
		// set oauth verifier
		$this->_oauth_verifier = $verifier;

		// check oauth token
		if (!strlen($this->_oauth_token)) {
			throw new HTTP_OAuthConsumer_Exception('oauth token is not set');
		}

		// send
		$res = $this->send();

		// check response body
		parse_str($res->getBody(), $access);
		if (!isset($access['oauth_token'], $access['oauth_token_secret'])) {
			$message = sprintf('Response body error: %s', $res->getBody());
			throw new HTTP_OAuthConsumer_Exception($message);
		}

		// set token
		$this->setToken($access['oauth_token'], $access['oauth_token_secret']);

		return $access;
	}


	/* HTTP options setter */

	public function addGetParameter($name, $value = null)
	{   
		if (!is_array($name)) {
			$this->getParams[$name] = $value;
		} else {
			foreach ($name as $k => $v) {
				$this->addGetParameter($k, $v);
			}   
		}   
		return $this;
	}


	/* OAuth options getter */

	public function getConsumerkey()
	{
		return $this->_consumer_key;
	}

	protected function getTimestamp()
	{
		return time();
	}

	protected function getNonce()
	{
		return md5(microtime().mt_rand());
	}

	protected function getVersion()
	{
		return '1.0';
	}


	/* override */

	public function setURL($url)
	{
		parent::setURL($url);
		parse_str($this->url->getQuery(), $this->getParams);
		$this->url->setQuery(false);
		return $this;
	}


	/* send */

	public function send()
	{
		// check url
		if (!$this->url instanceof Net_URL2) {
			throw new HTTP_OAuthConsumer_Exception('No URL given');
		}

		// check consumer
		if (empty($this->_consumer_key) || empty($this->_consumer_secret)) {
			throw new HTTP_OAuthConsumer_Exception('No consumer given');
		}

		// init oauth param
		$this->_oauth = array(
			'oauth_consumer_key'	=> $this->getConsumerKey(),
			'oauth_signature_method'=> $this->getSignatureMethod(),
			'oauth_timestamp'		=> $this->getTimestamp(),
			'oauth_nonce'			=> $this->getNonce(),
			'oauth_version'			=> $this->getVersion()
		);

		// add oauth token
		if (strlen($this->_oauth_token)) {
			$this->_oauth['oauth_token'] = $this->_oauth_token;
		}

		// add oauth callback
		if (strlen($this->_oauth_callback)) {
			$this->_oauth['oauth_callback'] = $this->_oauth_callback;
		}

		// add oauth verifier
		if (strlen($this->_oauth_verifier)) {
			$this->_oauth['oauth_verifier'] = $this->_oauth_verifier;
		}

		// set default content type
		if (empty($this->headers['content-type'])) {
			$this->setHeader('content-type', 'application/x-www-form-urlencoded');
		}

		// http method is get
		if (parent::METHOD_GET==$this->getMethod()) {
			$this->postParams = array();
		}

		// set get parameters
		$this->url->setQuery(http_build_query($this->getParams));

		// add oauth body hash
		$body = (string)$this->getBody();
		if ($this->headers['content-type']!='application/x-www-form-urlencoded' && strlen($body)) {
			$this->_oauth['oauth_body_hash'] = base64_encode(sha1($body, true));
		}

		// add oauth_signature param
		$this->_oauth['oauth_signature'] = $this->_makeSignature();
		ksort($this->_oauth);

		// make auth header
		$auth = sprintf('OAuth realm="%s"', self::urlencode_rfc3986($this->_realm));
		foreach ($this->_oauth as $k=>$v) {
			$auth .= sprintf(', %s="%s"', self::urlencode_rfc3986($k), self::urlencode_rfc3986($v));
		}
		$this->setHeader('authorization', $auth);

		// send
		$res = parent::send();
		$this->_last_response = $res;

		// check response status
		if ($res->getStatus()!=200) {
			$message = sprintf('Response status error: %s', $res->getBody());
			throw new HTTP_OAuthConsumer_Exception($message);
		}

		return $res;
	}

	protected function _makeSignatureBaseString()
	{
		// url
		$net_url = $this->getURL();
		if (!$net_url instanceof Net_URL2) {
			throw new HTTP_OAuthConsumer_Exception('No URL given');
		}
		$url = sprintf('%s://%s%s', $net_url->getScheme(), $net_url->getHost(), $net_url->getPath());

		// param
		$params = array_merge($this->getParams, $this->postParams, $this->_oauth);
		ksort($params);
		$params_str = self::http_build_query_rfc3986($params);

		$base = array(
			self::urlencode_rfc3986($this->getMethod()),
			self::urlencode_rfc3986($url),
			self::urlencode_rfc3986($params_str)
		);
		return implode('&', $base);
	}

	public function getLastResponse()
	{
		return $this->_last_response;
	}


	/* abstract */

	abstract public function getSignatureMethod();
	abstract protected function _makeSignature();


	/* utils */

	protected static function urlencode_rfc3986($str)
	{
		$str = rawurlencode($str);
		$str = str_replace('%7E', '~', $str);
		$str = str_replace('+', ' ', $str);
		return $str;
	}

	protected static function http_build_query_rfc3986($params)
	{
		$tmp = array();
		foreach ($params as $key=>$value) {
			$key = self::urlencode_rfc3986($key);
			$value = self::urlencode_rfc3986($value);
			$tmp[] = sprintf('%s=%s', $key, $value);
		}
		return implode('&', $tmp);
	}
}
