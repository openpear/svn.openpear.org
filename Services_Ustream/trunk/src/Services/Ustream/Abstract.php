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

require_once 'HTTP/Request2.php';
require_once 'Services/Ustream/Exception.php';
require_once 'Services/Ustream/Result.php';

abstract class Services_Ustream_Abstract
{
    protected $_baseUrl = 'http://api.ustream.tv';
    protected $_apiKey;
    protected $_subject;
    protected $_params;
    protected $_responseType;
    protected $_request;

    public function __construct($apiKey = '', $responseType = 'php', $config = array())
    {
        if (isset($apiKey)) {
            $this->setApiKey($apiKey);
        }
        $this->setResponseType($responseType);
        
        if ($this->_request == '') {
            $this->_request = new HTTP_Request2();
            $this->_request->setConfig($config);
            $this->_request->setHeader('User-Agent', 'Services_Ustream');
        }
    }

    protected function _sendRequest()
    {
        if (!$this->_apiKey) {
            throw new Services_Ustream_Exception('Empty API Key');
        }
        $this->setParam('key', $this->_apiKey);
        $this->setParam('subject', $this->_subject);
        $url = sprintf('%s/%s?%s', $this->_baseUrl, $this->_responseType, http_build_query($this->_params));
        try {
            $response = $this->_request->setUrl($url)->send();
            if ($response->getStatus() == 200) {
                if ($this->_responseType == 'xml' || $this->_responseType == 'php') {
                    return new Services_Ustream_Result($response->getBody(), $this->_responseType);
                } else {
                    return $response->getBody();
                }
            } else {
                throw new Services_Ustream_Exception('Server returned status: ' . $response->getStatus());
            }
        } catch (HTTP_Request2_Exception $e) {
            throw new Services_Ustream_Exception($e->getMessage(), $e->getCode());
        }
    }

    public function setApiKey($apiKey)
    {
        $this->_apiKey = $apiKey;

        return $this;
    }

    public function setResponseType($type)
    {
        if (in_array($type, array('xml', 'json', 'php', 'html'))) {
            $this->_responseType = $type;
        } else {
            throw new Services_Ustream_Exception('Invalid response type.');
        }
        return $this;
    }
    
    public function setParam($name, $value)
    {
        $this->_params[$name] = $value;
        return $this;
    }

    public function setPage($page)
    {
        return $this->setParam('page', (int) $page);
    }

    public function setLimit($limit)
    {
        return $this->setParam('limit', (int) $limit);
    }



    public function clearParams()
    {
        $this->_params = array();
        
        return $this;
    }
}

