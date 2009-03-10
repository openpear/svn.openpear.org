<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
/**
 * Services_Ustream
 *
 * PHP version 5
 *
 * LICENSE
 *
 * Copyright (c) 2009, Kimiaki Makino <makino@gagne.jp>
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *    * Redistributions of source code must retain the above copyright
 *      notice, this list of conditions and the following disclaimer.
 *    * Redistributions in binary form must reproduce the above copyright
 *      notice, this list of conditions and the following disclaimer in the
 *      documentation and/or other materials provided with the distribution.
 *    * The name of the author may not be used to endorse or promote products
 *      derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS
 * IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO,
 * THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR
 * CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
 * EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
 * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR
 * PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY
 * OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
 * NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @category  Services
 * @package   Services_Ustream
 * @author    Kimiaki Makino <makino@gagne.jp>
 * @copyright 2009 Kimiaki Makino
 * @license   http://opensource.org/licenses/bsd-license.php New BSD License
 * @version   SVN: $Id$
 * @link      http://openpear.otg/package/Services_Ustream
 * @since     File available since Release 0.1
 */


/**
 * Uses HTTP_Request2 class to send and receive data from Ustream API server.
 */
require_once 'HTTP/Request2.php';

/**
 * Uses Services_Ustream_Exception class for exception.
 */
require_once 'Services/Ustream/Exception.php';

/**
 * Uses Services_Ustream_Result class for result
 */
require_once 'Services/Ustream/Result.php';


/**
 * Abstract class for Services_Ustream
 *
 * @category  Services
 * @package   Services_Ustream
 * @author    Kimiaki Makino <makino@gagne.jp>
 * @copyright 2009 Kimiaki Makino
 * @license   http://opensource.org/licenses/bsd-license.php New BSD License
 * @link      http://openpear.otg/package/Services_Ustream
 */

abstract class Services_Ustream_Abstract
{
    protected $baseUrl = 'http://api.ustream.tv';
    protected $apiKey;
    protected $subject;
    protected $requestParams;
    protected $responseType;
    protected $request;

    /**
     * Constructor
     *
     * @param string $apiKey       Ustream API Key.
     * @param string $responseType Response type (xml,json,php,html)
     * @param array  $config       Settings for HTTP_Request2
     */
    
    public function __construct($apiKey = '', $responseType = 'php', $config = array())
    {
        if (isset($apiKey)) {
            $this->setApiKey($apiKey);
        }
        $this->setResponseType($responseType);
        
        if ($this->request == '') {
            $this->request = new HTTP_Request2();
            $this->request->setConfig($config);
            $this->request->setHeader('User-Agent', 'Services_Ustream');
        }
    }

    /**
     * Send request to server.
     *
     * @return mixed Services_Ustream_Result object or result string.
     */
    protected function sendRequest()
    {
        if (!$this->apiKey) {
            throw new Services_Ustream_Exception('Empty API Key');
        }
        $this->setParam('key', $this->apiKey);
        $this->setParam('subject', $this->subject);
        $url = sprintf(
            '%s/%s?%s',
            $this->baseUrl,
            $this->responseType,
            http_build_query($this->requestParams)
        );
        try {
            $response = $this->request->setUrl($url)->send();
            if ($response->getStatus() == 200) {
                if ($this->responseType == 'xml'
                    || $this->responseType == 'php'
                ) {
                    return new Services_Ustream_Result($response->getBody(),
                                                       $this->responseType);
                } else {
                    return $response->getBody();
                }
            } else {
                throw new Services_Ustream_Exception('Server returned status: '
                                                     . $response->getStatus());
            }
        } catch (HTTP_Request2_Exception $e) {
            throw new Services_Ustream_Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Set API Key
     *
     * @param string $apiKey Ustream API key.
     *
     * @return object
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;

        return $this;
    }

    /**
     * Set API response type.
     *
     * @param string $type Response type (xml,json,php,html)
     *
     * @return object
     */
    public function setResponseType($type)
    {
        if (in_array($type, array('xml', 'json', 'php', 'html'))) {
            $this->responseType = $type;
        } else {
            throw new Services_Ustream_Exception('Invalid response type.');
        }
        return $this;
    }
    
    /**
     * Set parameter for request
     *
     * @param string $name  name
     * @param string $value value
     *
     * @return object
     */
    public function setParam($name, $value)
    {
        $this->requestParams[$name] = $value;
        return $this;
    }

    /**
     * Set page of result.
     *
     * @param integer $page page num
     *
     * @return object
     */
    public function setPage($page)
    {
        return $this->setParam('page', (int) $page);
    }
    
    /**
     * Set limit of result
     *
     * @param interger $limit Limit
     *
     * @return object
     */
    public function setLimit($limit)
    {
        return $this->setParam('limit', (int) $limit);
    }


    /**
     * Clear parameters
     *
     * @return object
     */
    public function clearParams()
    {
        $this->requestParams = array();
        
        return $this;
    }
}

