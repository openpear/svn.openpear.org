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

class Services_Ustream_Channel extends Services_Ustream_Abstract
{
    public function getInfo($uid)
    {
        $url = sprintf('%s/%s/channel/%s/getInfo',
                    self::API_URI,
                    $this->getResponseType(),
                    $uid);
        $this->_send($url, array('key' => $this->getApiKey()));
        return $this->getResult()->results;
    }

    public function getValueOf($uid, $key)
    {
        $url = sprintf('%s/%s/channel/%s/getValueOf/%s',
                    self::API_URI,
                    $this->getResponseType(),
                    $uid,
                    $key);
        $this->_send($url, array('key' => $this->getApiKey()));
        return $this->getResult()->results;
    }

    public function getId($channel)
    {
        return $this->getValueOf($channel, 'id');
        
    }

    public function getEmbedTag($uid)
    {
        $url = sprintf('%s/%s/channel/%s/getEmbedTag',
                    self::API_URI,
                    $this->getResponseType(),
                    $uid);
        $this->_send($url, array('key' => $this->getApiKey()));
        return $this->getResult()->results;
    }

    /**
     * getCustomEmbedTag
     * 
     * @param string|integer $uid
     * @param array $params
     * @return string
     */
    public function getCustomEmbedTag($uid, $params = array())
    {
        $url = sprintf('%s/%s/channel/%s/getCustomEmbedTag',
                self::API_URI,
                $this->getResponseType(),
                $uid);

         $this->_send($url, array('key' => $this->getApiKey(), 'params' => implode(';', $params)));
         return $this->getResult()->results;
        
    }

    public function listAllChannels($uid)
    {
        $url = sprintf('%s/%s/channel/%s/listAllChannels',
                    self::API_URI,
                    $this->getResponseType(),
                    $uid);
        $this->_send($url, array('key' => $this->getApiKey()));
        
        if ($this->getResponseType() == 'xml') {
            $results = $this->getResult()->results;
            return $results['array'];
        } else {
            return $this->getResult()->results;
        }
    }

    public function getComments($uid)
    {
        $url = sprintf('%s/%s/channel/%s/getComments',
                    self::API_URI,
                    $this->getResponseType(),
                    $uid);
        $this->_send($url, array('key' => $this->getApiKey()));
        
        if ($this->getResponseType() == 'xml') {
            $results = $this->getResult()->results;
            return $results['array'];
        } else {
            return $this->getResult()->results;
        }
    }

    public function getTags($uid)
    {
        require_once 'Services/Ustream/Exception.php';
        throw new Services_Ustream_Exception('******');
        return;
        $url = sprintf('%s/%s/channel/%s/getTags',
                    self::API_URI,
                    $this->getResponseType(),
                    $uid);
        $this->_send($url, array('key' => $this->getApiKey()));
        
        if ($this->getResponseType() == 'xml') {
            $results = $this->getResult()->results;
            return $results['array'];
        } else {
            return $this->getResult()->results;
        }
    }

    /**
     *
     * @return Services_Ustream_Search
     */
    public function search()
    {
        return $this->_getSearchInstance('channel');
    }
}