<?php
/**
 *
 *
 */

require_once dirname(__FILE__) . '/Common.php';

/**
 *
 *
 */
class Services_2chClient_Thread extends Services_2chClient_Common
{
    /**
     * __construct
     *
     * @todo now implement
     */
    public function __construct($url, $board_key, $thread_key)
    {
        parent::__construct($url, $board_key, $thread_key);

        $url = 'http://' . $url . '/' . $board_key . '/dat/' . $thread_key . '.dat';
        $body = $this->fetch($url);

        
        var_dump($body);
    }

    /**
     * fetch
     *
     * @todo Commonへ
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
}

