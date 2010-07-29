<?php
class Net_Curl2
{
	private $_ch = null;
	private $_header = array();

	private $_res_info = array();
	private $_res_head = array();
	private $_res_body = null;


	public function __construct($url=null, $method=null)
	{
		$this->_ch = curl_init($url);
		if (isset($method)) {
			$this->setMethod($method);
		}
		$this->setConnectTimeout(2);
		$this->setTimeout(5);
	}


	// set option

	public function setMethod($method)
	{
		$method = strtoupper($method);
		$this->setopt(CURLOPT_CUSTOMREQUEST, $method);
	}

	public function setURL($url, $param=array())
	{
		if (0<count($param)) {
			if (strpos($url, '?')) {
				list($url, $param_str) = explode('?', $url, 2);
				parse_str($param_str, $param2);
				$param = array_merge($param2, $param);
			}
			$url .= sprintf('?%s', http_build_query($param));
		}
		curl_setopt($this->_ch, CURLOPT_URL, $url);
	}

	public function setHeader($key, $value=null)
	{
		if (is_array($key)) {
			$this->_header = array_merge($this->_header, $key);
		} else {
			$this->_header[$key] = $value;
		}
	}

	public function setBody($value)
	{
		$this->setopt(CURLOPT_POSTFIELDS, $value);
	}

	public function setConnectTimeout($value, $ms=false)
	{
		if ($ms) {
			$this->setopt(CURLOPT_CONNECTTIMEOUT_MS, $value);
		} else {
			$this->setopt(CURLOPT_CONNECTTIMEOUT, $value);
		}
	}

	public function setTimeout($value, $ms=false)
	{
		if ($ms) {
			$this->setopt(CURLOPT_TIMEOUT_MS, $value);
		} else {
			$this->setopt(CURLOPT_TIMEOUT, $value);
		}
	}

	public function setAuth($username, $password)
	{
		$this->setopt(CURLOPT_USERPWD, $username.':'.$password);
	}

	public function setPort($port)
	{
		$this->setopt(CURLOPT_PORT, $port);
	}

	public function setopt($key, $value)
	{
		curl_setopt($this->_ch, $key, $value);
	}


	// request

	public function request()
	{
		// init
		$this->_res_info = array();
		$this->_res_head = array();
		$this->_res_body = null;

		// set request headers
		$this->setopt(CURLOPT_HTTPHEADER, $this->_header);
		// follow location
		$this->setopt(CURLOPT_FOLLOWLOCATION, true);
		$this->setopt(CURLOPT_MAXREDIRS, 5);
		// get header flag
		$this->setopt(CURLOPT_HEADER, true);
		// return response flag
		$this->setopt(CURLOPT_RETURNTRANSFER, true);
		$this->setopt(CURLOPT_BINARYTRANSFER, true);

		// execute request
		$res = curl_exec($this->_ch);
		// get info
		$info = curl_getinfo($this->_ch);
		// error
		if ($info['total_time']==0) {
			throw new Exception('Net_Curl2 timeout');
		}

		// parse
		list($this->_res_body, $this->_res_header) = Net_Curl2::parseResponse($res, $info);
		$this->_res_info = $info;
	}


	// response

	public function getInfo($key=null)
	{
		if (is_null($key)) {
			return $this->_res_info;
		} else if (isset($this->_res_info[$key])) {
			return $this->_res_info[$key];
		}
		return null;
	}

	public function getStatus()
	{
		return isset($this->_res_info['http_code']) ? $this->_res_info['http_code'] : 0;
	}

	public function getHeader($key=null)
	{
		if (is_null($key)) {
			return $this->_res_header;
		} else if (isset($this->_res_header[$key])) {
			return $this->_res_header[$key];
		}
		return null;
	}

	public function getBody()
	{
		return $this->_res_body;
	}


	// util

	public static function parseResponse($res, $info)
	{
		$head = array();
		$continue = 0;
		for ($i=0; $i<=$info['redirect_count']+$continue; $i++) {
			$eol = Net_Curl2::getEOL($res);
			@list($head_str, $res) = explode($eol.$eol, $res, 2);
			if (!isset($head_str, $res)) {
				break;
			}
			list($code, $head) = Net_Curl2::parseHeader($head_str, $eol);
			if ($code==100) {
				$continue++;
			}
		}
		return array($res, $head);
	}

	public static function parseHeader($head, $eol="\r\n")
	{
		$code = 0;
		$header = array();
		$items = explode($eol, $head);
		foreach ($items as $i=>$item) {
			if (0<strlen($item)) {
				if ($i==0) {
					list($protocol, $code, $message) = explode(' ', $item, 3);
				} else if (strpos($item, ':')) {
					list($key, $value) = explode(':', $item, 2);
					$header[trim($key)] = trim($value);
				}
			}
		}
		return array($code, $header);
	}

	public static function getEOL($data) {
		$eol = "\r\n";
		$index = null;
		$eol_arr = array("\r\n", "\r", "\n");
		foreach ($eol_arr as $eol_tmp) {
			$index_tmp = strpos($data, $eol_tmp);
			if ($index_tmp!==false && ($index==null || $index_tmp<$index)) {
				$eol = $eol_tmp;
				$index = $index_tmp;
			}
		}
		return $eol;
	}
}
