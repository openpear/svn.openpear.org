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

require_once 'Services/Ustream/Abstract.php';

class Services_Ustream_Search extends Services_Ustream_Abstract
{
    protected $_subject = 'search';
    protected $_command;
    protected $_scopeOrSorting;
    protected $_searchParams = array();
    
    public function command($command)
    {
        $_commands = array('user', 'channel', 'stream', 'video');
        if (in_array($command, $_commands)) {
            $this->_command = $command;
        } else {
            throw new Services_Ustream_Exception('Invalid command.');
        }
        return $this;
    }

    public function scope($scope)
    {
        $this->_scopeOrSorting = $scope;
        return $this;
    }

    public function uid($uid)
    {
        $this->_scopeOrSorting = $uid;
        return $this;
    }

    public function newest($flag = true)
    {
        $this->_scopeOrSorting =
            ($flag) ? 'newest' : '!newest';
        return $this;
    }

    public function recent($flag = true)
    {
        $this->_scopeOrSorting =
            ($flag) ? 'recent' : '!recent';
        return $this;
    }

    public function all()
    {
        $this->_scopeOrSorting = 'all';
        return $this;
    }

    public function live()
    {
        $this->_scopeOrSorting = 'live';
        return $this;
    }

    public function popular()
    {
        $this->_scopeOrSorting = 'popular';
        return $this;
    }

    public function where($key)
    {
        $this->_searchParams[0] = $key;
        return $this;
    }

    public function like($value)
    {
        $this->_searchParams[1] = 'like:' . $value;
        return $this;
    }

    public function eq($value)
    {
        $this->_searchParams[1] = 'eq:' . $value;
        return $this;
    }

    public function lt($value)
    {
        $this->_searchParams[1] = 'lt:' . $value;
        return $this;
    }

    public function gt($value)
    {
        $this->_searchParams[1] = 'gt:' . $value;
        return $this;
    }

    public function query()
    {
        $params = implode(':', $this->_searchParams);
        $url = sprintf('%s/%s/%s/%s/%s/%s?key=%s',
                        $this->_baseUrl,
                        $this->_responseType,
                        $this->_command,
                        $this->_scopeOrSorting,
                        $this->_subject,
                        $params,
                        $this->_apiKey);
        echo $url;
        
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



}
