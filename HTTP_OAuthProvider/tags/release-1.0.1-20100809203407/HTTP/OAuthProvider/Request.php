<?php
require_once(dirname(__FILE__).'/Exception.php');

class HTTP_OAuthProvider_Request
{
	protected $_method = null;
	protected $_header = null;
	protected $_params = null;
	protected $_signature = null;
	protected $_body = null;


	/* construct */

	public function __construct()
	{
		$this->_initialize();
	}

    protected function _initialize()
    {
        static $initialized = false;
        if ($initialized) {
            return;
        }

        // header
        $this->_header = array_change_key_case(apache_request_headers(), CASE_UPPER);

        // HTTP Method
        $this->_method = $_SERVER['REQUEST_METHOD'];
        if (isset($this->_header['HTTP_X_HTTP_METHOD_OVERRIDE'])) {
            $this->_method = $this->_header['HTTP_X_HTTP_METHOD_OVERRIDE'];
        }

        // parameters
        list($this->_params, $this->_signature) = $this->_parseRequestParameters();

        // body
        $this->_body = file_get_contents('php://input');

        $initialized = true;
    }


	/* getter */

	public function getMethod()
	{
		return $this->_method;
	}

	public function getHeader($key=null)
	{
		if (isset($key)) {
			$key = strtoupper($key);
			if (isset($this->_header[$key])) {
				return $this->_header[$key];
			}
			return null;
		}
		return $this->_header;
	}

	public function getParameter($key=null)
	{
		if (isset($key)) {
			if (isset($this->_params[$key])) {
				return $this->_params[$key];
			}
			return null;
		}
		return $this->_params;
	}

	public function getSignature()
	{
		return $this->_signature;
	}

	public function getBody()
	{
		return $this->_body;
	}


    /**
     * checkParameters
     * 
     * Check necessary parameters
     * 
     * @param Array   $params          Necessary parameters
     * @param Boolean $check_signature is signature checked?
     * 
	 * @return Boolean
	 */
	public function checkParameters($keys, $check_signature=false)
	{
		$noparam = array();
		foreach ($keys as $key) {
			if (!$this->getParameter($key)) {
				$noparam[] = $key;
			}
		}
		if ($check_signature && !$this->getSignature()) {
			$noparam[] = 'oauth_signature';
		}
		if (0<count($noparam)) {
			throw new HTTP_OAuthProvider_Exception('400 OAuth parameter(s) does not exist: '.implode(', ', $noparam), 400);
		}
		return true;
	}

    /**
     * checkBodyHash
     * 
     * check oauth_body_hash
     * 
     * @return Boolean
     */
    public function checkBodyHash()
    {
        if (!isset($this->_header['CONTENT-TYPE']) || $this->_header['CONTENT-TYPE']!='application/x-www-form-urlencoded') {
            if (0<strlen($this->getBody())) {
                if (is_null($this->getParameter('oauth_body_hash'))) {
                    throw new HTTP_OAuthProvider_Exception('400 OAuth parameter(s) does not exist: oauth_body_hash', 400);
                }
                if ($this->getParameter('oauth_body_hash')!=base64_encode(sha1($this->getBody(), true))) {
                    throw new HTTP_OAuthProvider_Exception('401 Body Hash is not valid', 401);
                }
            }
        }
		return true;
    }


    /* privat method */

    /**
     * _parseRequestParameters
     * 
     * Parse request parameters
     * 
     * @return Array
     */
    protected function _parseRequestParameters()
    {
        // get
        $params = $_GET;

        // post
        if (isset($this->_header['CONTENT-TYPE']) && $this->_header['CONTENT-TYPE']=='application/x-www-form-urlencoded') {
            $params = array_merge($params, $_POST);
        }

        // header
        if (isset($this->_header['AUTHORIZATION']) && strpos($this->_header['AUTHORIZATION'], 'OAuth ')===0) {
            $params_tmp = preg_split("/,[\r\n\s]*/", $this->_header['AUTHORIZATION']);
            foreach ($params_tmp as $v) {
                if (preg_match('/^([^"= ]+)="([^"]*)"$/', $v, $m)) {
                    $params[urldecode($m[1])] = urldecode($m[2]);
                }
            }
        }
        if (isset($params['realm'])) {
            unset($params['realm']);
        }

        // oauth_signature
        $signature = null;
        if (isset($params['oauth_signature'])) {
            $signature = $params['oauth_signature'];
            unset($params['oauth_signature']);
        }

        return array($params, $signature);
    }
}
