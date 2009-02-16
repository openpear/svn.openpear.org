<?php
require_once 'Service/Ustream/Result.php';

abstract class Service_Ustream_Abstract
{
	const API_URI = 'http://api.ustream.tv';
    protected $_responseTypes = array('xml', 'json', 'php', 'html');
	protected $_apiKey;
    protected $_respnseType;

    protected $_rest;
    protected $_response;
    protected $_result;

    /**
     * Constructor
     *
     * @param string $apiKey
     */
	public function __construct($apiKey = null, $responseType = 'php')
	{
		if (!is_null($apiKey)) {
			$this->setApiKey($apiKey);
		}
        $this->setResponseType($responseType);
        $rest = new HTTP_Request2(self::API_URI);
        $rest->setAdapter('HTTP_Request2_Adapter_Curl')
             ->setHeader('User-Agent', 'Service_Ustream/' . Service_Ustream::VERSION);
        $this->_rest = $rest;
	}

    /**
     * Set API Key.
     *
     * @param string $apiKey
     * @return Service_Ustream_Abstract
     */
	public function setApiKey($apiKey)
	{
		$this->_apiKey = $apiKey;
        return $this;
	}

    /**
     * Get API Key.
     * 
     * @return string API key.
     */
    public function getApiKey()
    {
        return $this->_apiKey;
    }

    /**
     * Set response type.
     * 
     * @param string $responseType
     * @throws Service_Ustream_Exception
     * @return Service_Ustream_Abstract
     */
    public function setResponseType($responseType = 'php')
    {
        if (!in_array($responseType, $this->_responseTypes, TRUE)) {
            throw new Service_Ustream_Exception('Invalid Response Type.');
        }
        $this->_respnseType = $responseType;
        return $this;
    }

    /**
     *  Get response type.
     * 
     * @return string Response Type.
     */
    public function getResponseType()
    {
        return $this->_respnseType;
    }

    /**
     *  Set REST Config.
     * 
     * @param array $restConfig
     * @return Service_Ustream_Abstract
     */
    public function setRestConfig(Array $restConfig = array())
    {
        $this->_rest->setConfig($restConfig);
        return $this;
    }

    /**
     *
     * @param string $url
     */
    protected function _send($url)
    {
        unset($this->_respnse);
        $this->_rest->setUrl($url);
        $response = $this->_rest->send();
        $this->_response = $response;
        $this->_result = new Service_Ustream_Result($response, $this->getResponseType());
    }

    public function getResult()
    {
        return $this->_result;
    }
}