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

require_once 'Service/Ustream/Abstract.php';

class Service_Ustream_Channel extends Service_Ustream_Abstract
{
	public function getInfo($uid)
    {
        $url = sprintf('%s/%s/channel/%s/getInfo?key=%s',
                    self::API_URI,
                    $this->getResponseType(),
                    $uid,
                    $this->getApiKey());
        $this->_send($url);
        return $this->getResult()->results;
    }

    public function getValueOf($uid, $key)
    {
        $url = sprintf('%s/%s/channel/%s/getValueOf/%s?key=%s',
                    self::API_URI,
                    $this->getResponseType(),
                    $uid,
                    $key,
                    $this->getApiKey());
        $this->_send($url);
        return $this->getResult()->results;
    }

    public function getId($channel)
    {
        return $this->getValueOf($channel, 'id');
        /*
         * Invild COMMAND getId 2009.02.16
        $url = sprintf('%s/%s/channel/%s/getId?key=%s',
                    self::API_URI,
                    $this->getResponseType(),
                    $channel,
                    $this->getApiKey());
        $this->_send($url);echo $url;
        $this->_result = new Service_Ustream_Result_Channel($this->_response, $this->getResponseType());
        return $this->_result->getId();
         */
    }

    public function getEmbedTag($uid)
    {
        $url = sprintf('%s/%s/channel/%s/getEmbedTag?key=%s',
                    self::API_URI,
                    $this->getResponseType(),
                    $uid,
                    $this->getApiKey());
        $this->_send($url);
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
        $url = sprintf('%s/%s/channel/%s/getCustomEmbedTag?key=%s%s',
                self::API_URI,
                $this->getResponseType(),
                $uid,
                $this->getApiKey(),
                ((is_array($params)) ? ('&params=' . implode(';', $params)) : null));
         $this->_send($url);
         return $this->getResult()->results;
        
    }

    public function listAllChannels($uid)
    {
        $url = sprintf('%s/%s/channel/%s/listAllChannels?key=%s',
                    self::API_URI,
                    $this->getResponseType(),
                    $uid,
                    $this->getApiKey());
        $this->_send($url);
        if ($this->getResponseType() == 'xml') {
            $results = $this->getResult()->results;
            return $results['array'];
        } else {
            return $this->getResult()->results;
        }
    }

    public function getComments($uid)
    {
        $url = sprintf('%s/%s/channel/%s/getComments?key=%s',
                    self::API_URI,
                    $this->getResponseType(),
                    $uid,
                    $this->getApiKey());
        $this->_send($url);
        if ($this->getResponseType() == 'xml') {
            $results = $this->getResult()->results;
            return $results['array'];
        } else {
            return $this->getResult()->results;
        }
    }

    public function getTags($uid)
    {
        require_once 'Service/Ustream/Exception.php';
        throw new Service_Ustream_Exception('******');
        return;
        $url = sprintf('%s/%s/channel/%s/getTags?key=%s',
                    self::API_URI,
                    $this->getResponseType(),
                    $uid,
                    $this->getApiKey());
        $this->_send($url);
        echo $url;
        if ($this->getResponseType() == 'xml') {
            $results = $this->getResult()->results;
            return $results['array'];
        } else {
            return $this->getResult()->results;
        }
    }
}