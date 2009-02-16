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

class Service_Ustream_User extends Service_Ustream_Abstract
{
    public function getInfo($user)
    {
        $url = sprintf("%s/%s/user/%s/getInfo?key=%s",
                    self::API_URI,
                    $this->getResponseType(),
                    $user,
                    $this->getApiKey()
        );

        $this->_send($url);
        return $this->getResult()->results;
        
    }
	public function getId($user)
    {
        $url = sprintf("%s/%s/user/%s/getId?key=%s",
                    self::API_URI,
                    $this->getResponseType(),
                    $user,
                    $this->getApiKey()
        );

        $this->_send($url);
        return $this->getResult()->results;
    }
    public function getValueOf($user, $key)
    {
        $url = sprintf("%s/%s/user/%s/getValueOf/%s?key=%s",
                    self::API_URI,
                    $this->getResponseType(),
                    $user,
                    $key,
                    $this->getApiKey()
        );

        $this->_send($url);
        return $this->getResult()->results;
    }

    public function listAllChannels($user)
    {
        $url = sprintf("%s/%s/user/%s/listAllChannels?key=%s",
                    self::API_URI,
                    $this->getResponseType(),
                    $user,
                    $this->getApiKey()
        );

        $this->_send($url);
        $this->_result = new Service_Ustream_Result_User($this->_response, $this->getResponseType());
        return $this->getResult()->results;
    }

    public function listAllVideos($user)
    {
        $url = sprintf("%s/%s/user/%s/listAllVideos?key=%s",
                    self::API_URI,
                    $this->getResponseType(),
                    $user,
                    $this->getApiKey()
        );

        $this->_send($url);
        return $this->getResult()->results;
    }

    public function search($command)
    {
        $url = sprintf("%s/%s/user/recent/search/%s?key=%s",
                    self::API_URI,
                    $this->getResponseType(),
                    $command,
                    $this->getApiKey()
                );
        $this->_send($url);
        return $this->getResult()->results;

    }
}