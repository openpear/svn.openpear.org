<?php
class Service_Ustream_Result
{
    protected $_response;
    protected $_responseType;
    protected $_results;
    public function __construct($response, $responseType)
    {
        $this->_response = $response;
        $this->_responseType = $responseType;
        $this->_results = array();
        $this->_parseResponse();
    }

    public function getResults()
    {
        return $this->_results;
    }

    protected function _parseResponse()
    {
        switch (strtolower($this->_responseType)) {
            case 'xml':
                $this->_parseResponseFromXml();
                break;
            case 'php':
                $this->_parseResponseFromPhp();
                break;
            case 'html':
            case 'json':
                $results = $this->_response->getBody();
                $this->_results['results'] = $results;
                break;
            default:
                require_once 'Service/Ustream/ResultException.php';
                throw new Service_Ustream_ResultException('Invalid response type.');
        }
    }

    protected function _parseResponseFromXml()
    {
        require_once 'XML/Unserializer.php';
        $xml = new XML_Unserializer;
        $xml->unserialize($this->_response->getBody());
        $results = $xml->getUnserializedData();
        if ($results['error']) {
            require_once 'Service/Ustream/ResultException.php';
            throw new Service_Ustream_ResultException($results['msg']);
        }
        $this->_results = $results;
    }

    protected function _parseResponseFromPhp()
    {
        $results = unserialize($this->_response->getBody());
        if ($results['error']) {
            require_once 'Service/Ustream/ResultException.php';
            throw new Service_Ustream_ResultException($results['msg']);
        }
        $this->_results = $results;
    }

    public function __get($name)
    {
        if (array_key_exists($name, $this->_results)) {
            return $this->_results[$name];
        }
    }
}


