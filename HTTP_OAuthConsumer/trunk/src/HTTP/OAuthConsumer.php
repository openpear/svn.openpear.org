<?php
require_once('HTTP/Request2.php');

abstract class HTTP_OAuthConsumer extends HTTP_Request2
{
	// oauth consumer
	protected $_consumer_key = null;
	protected $_consumer_secret = null;

	// oauth token
	protected $_oauth_token = '';
	protected $_oauth_token_secret = '';

	// oauth parameters
	protected $_oauth = array();
	protected $_realm = '';


	/* factory */

	public static function factory($sig_method='HMAC-SHA1')
	{
		$sig_method = str_replace('-', '_', $sig_method);

		$file = sprintf('%s/OAuthConsumer/%s.php', dirname(__FILE__), $sig_method);
		$class = sprintf('HTTP_OAuthConsumer_%s', $sig_method);
		if (!file_exists($file)) {
			throw new Exception('No such signature method');
		}
		require_once($file);
		if (!class_exists($class)) {
			throw new Exception('No such signature class');
		}
        return new $class;
	}

	/* OAuth options setter */

	public function setConsumer($consumer_key, $consumer_secret)
	{
		$this->_consumer_key = $consumer_key;
		$this->_consumer_secret = $consumer_secret;
	}

	public function setOAuthToken($oauth_token, $oauth_token_secret)
	{
		$this->_oauth_token = $oauth_token;
		$this->_oauth_token_secret = $oauth_token_secret;
	}

	public function setRealm($realm)
	{
		$this->_realm = $realm;
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


	/* send */

	public function send()
	{
		// parameter check
		if (empty($this->_consumer_key) || empty($this->_consumer_secret)) {
			throw new Exception('No consumer given');
		}

		// remove oauth_* parameters
		foreach (array_keys($this->postParams) as $key) {
			if (strpos($key, 'oauth_')===0) {
				unset($this->postParams[$key]);
			}
		}

		// init oauth param
		$this->_oauth = array(
			'oauth_consumer_key'	=> $this->getConsumerKey(),
			'oauth_signature_method'=> $this->getSignatureMethod(),
			'oauth_timestamp'		=> $this->getTimestamp(),
			'oauth_nonce'			=> $this->getNonce(),
			'oauth_version'			=> $this->getVersion()
		);

		// add oauth_token
		if (strlen($this->_oauth_token)) {
			$this->_oauth['oauth_token'] = $this->_oauth_token;
		}

		// set default content type
		if (empty($this->headers['content-type'])) {
			$this->setHeader('content-type', 'application/x-www-form-urlencoded');
		}

		// http method is get
		if (HTTP_Request2::METHOD_GET==$this->getMethod() && $this->postParams) {
			$query = $this->url->getQuery();
			if (strlen($query)) {
				$query .= '&';
			} else {
				$query = '';
			}
			$query .= http_build_query($this->postParams);
			$this->url->setQuery($query);
			$this->postParams = array();
		}

		// add oauth body hash
		$body = (string)$this->getBody();
		if ($this->headers['content-type']!='application/x-www-form-urlencoded' && strlen($body)) {
			$this->_oauth['oauth_body_hash'] = base64_encode(sha1($body, true));
		}

		// add oauth_signature param
		$this->_oauth['oauth_signature'] = $this->_makeSignature();
		ksort($this->_oauth);

		// make auth header
		$auth = sprintf('OAuth realm="%s"', HTTP_OAuthConsumer::urlencode_rfc3986($this->_realm));
		foreach ($this->_oauth as $k=>$v) {
			$auth .= sprintf(', %s="%s"', HTTP_OAuthConsumer::urlencode_rfc3986($k), HTTP_OAuthConsumer::urlencode_rfc3986($v));
		}
		$this->setHeader('authorization', $auth);

		return HTTP_Request2::send();
	}

	protected function _makeSignatureBaseString()
	{
		// url
		$net_url = $this->getURL();
		if (!$net_url instanceof Net_URL2) {
			throw new HTTP_Request2_Exception('No URL given');
		}
		$url = sprintf('%s://%s%s', $net_url->getScheme(), $net_url->getHost(), $net_url->getPath());

		// param
		$query = array();
		if ($net_url->getQuery()) {
			parse_str($net_url->getQuery(), $query);
		}
		$params = array_merge($this->postParams, $query, $this->_oauth);
		ksort($params);

		$base = array(
			HTTP_OAuthConsumer::urlencode_rfc3986($this->getMethod()),
			HTTP_OAuthConsumer::urlencode_rfc3986($url),
			HTTP_OAuthConsumer::urlencode_rfc3986(http_build_query($params))
		);
		return implode('&', $base);
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
}
