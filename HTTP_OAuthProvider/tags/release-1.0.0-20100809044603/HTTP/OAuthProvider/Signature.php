<?php

class HTTP_OAuthProvider_Signature
{
	protected $_provider = null;


	/* construct */

	public function __construct(HTTP_OAuthProvider $provider)
	{
		$this->_provider = $provider;
	}


    /**
     * _getSignatureBaseString
     * 
     * Return a signature base string
     * 
     * @param Array $param Received OAuth parameters
     * 
     * @return String
     */
    protected function _getSignatureBaseString()
    {
        $schema = 'http';
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on') {
            $schema = 'https';
        }
        $path = preg_replace('/\?.*$/', '', $_SERVER['REQUEST_URI']);
        $url = sprintf('%s://%s%s', $schema, $_SERVER['HTTP_HOST'], $path);
        $params = self::http_build_query_rfc3986($this->_provider->getRequest()->getParameter());

        $base = array(
            self::urlencode_rfc3986($this->_provider->getRequest()->getMethod()),
            self::urlencode_rfc3986($url),
            self::urlencode_rfc3986($params)
        );  
        return implode('&', $base);
    }


	/* static */

    public static function urlencode_rfc3986($str)
    {   
        $str = rawurlencode($str);
        $str = str_replace('%7E', '~', $str);
        $str = str_replace('+', ' ', $str);
        return $str;
    }   

    public static function http_build_query_rfc3986($params)
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
