<?php
/**
 * Common.php
 *
 * @package Services_2chClient
 */

/**
 * Services_2chClient_Common
 * 
 */
class Services_2chClient_Common
{
    /**
     * ユーザーエージェント
     */
    protected $_userAgent = 'Monazilla/1.00 (Services_2chClient)';

    /**
     * 最終更新日時
     */
    protected $_lastModified;

    /**
     * HTTP_Request
     */
    protected $http_req;

    /**
     * construct
     *
     */
    public function __construct()
    {
        $this->http_req = new HTTP_Request();
        $this->http_req->addHeader('User-Agent', $this->_userAgent);
    }

    /**
     * fetch
     *
     */
    public function fetch($url)
    {
        $this->http_req->setURL($url);

        if ($this->_lastModified) {
            $httpObject->addHeader('If-Modified-Since', $this->_lastModified);
        }

        $response = $this->http_req->sendRequest();

        if (PEAR::isError($response)) {
            throw new Exception($response->getMessage());
        }

        $responseCode = $this->http_req->getResponseCode();
        if ($responseCode != "200") {
            throw new Exception("Invalid response code:{$responseCode}, url:{$url}");
        }

        return $this->http_req->getResponseBody();
    }

    /**
     * setProperty
     *
     */
    public function setProperty($name, $value)
    {
        $name = '_' . $name;
        $this->$name = $value;
    }

    /**
     * getProperty
     *
     */
    public function getProperty($name)
    {
        $name = '_' . $name;
        return $this->$name;
    }
}
