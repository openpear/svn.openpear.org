<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
/**
 * Services_Ustream
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
 * @copyright  2009 Kimiaki Makino
 * @license http://opensource.org/licenses/bsd-license.php New BSD License
 * @version $Id$
 */

require_once 'Services/Ustream/Result.php';

abstract class Services_Ustream_Abstract
{
    const API_URI = 'http://api.ustream.tv';
    protected $_responseTypes = array('xml', 'json', 'php', 'html');
    protected $_apiKey;
    protected $_respnseType;
    protected $_page;
    protected $_limit;
    
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
             ->setHeader('User-Agent', 'Services_Ustream/' . Services_Ustream::VERSION);
        $this->_rest = $rest;
    }

    /**
     * Set API Key.
     *
     * @param string $apiKey
     * @return Services_Ustream_Abstract
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
     * @throws Services_Ustream_Exception
     * @return Services_Ustream_Abstract
     */
    public function setResponseType($responseType = 'php')
    {
        if (!in_array($responseType, $this->_responseTypes, TRUE)) {
            require_once 'Services/Ustream/Exception.php';
            throw new Services_Ustream_Exception('Invalid Response Type.');
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
     * @return Services_Ustream_Abstract
     */
    public function setRestConfig(Array $restConfig = array())
    {
        $this->_rest->setConfig($restConfig);
        return $this;
    }

    /**
     *  Set page num.
     * @param integer $page
     * @return Services_Ustream_Abstract
     */
    public function setPage($page)
    {
        $this->_page = (int) $page;
        return $this;
    }

    /**
     * Set limit.
     * @param integer $limit
     * @return Services_Ustream_Abstract
     */
    public function setLimit($limit)
    {
        $this->_limit = (int) $limit;
        return $this;
    }

    /**
     *
     * @param string $url
     */
    protected function _send($url, $params)
    {
        if ($this->_response) {
            unset($this->_response);
        }
        if ($this->_result) {
            unset($this->_result);
        }
        if ($this->_page) {
            $params['page'] = $this->_page;
        }
        if ($this->_limit && $this->_limit <= 20) {
            $params['limit'] = $this->_limit;
        }
        $url = $url . '?' . http_build_query($params);
        $this->_rest->setUrl($url);
        $response = $this->_rest->send();
        $this->_response = $response;
        $this->_result = new Services_Ustream_Result($response, $this->getResponseType());
    }

    public function getResult()
    {
        return $this->_result;
    }

    /**
     *
     * @param string $command
     * @return Services_Ustream_Search
     */
    protected function _getSearchInstance($command)
    {
        $search = Services_Ustream::factory('search');
        $search->setApiKey($this->getApiKey())
               ->setResponseType($this->getResponseType())
               ->command($command);
        return $search;
    }

}