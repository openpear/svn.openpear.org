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

class Services_Ustream_Stream extends Services_Ustream_Abstract
{
    /**
     * 
     * @return array
     */
    public function getRecent()
    {
        $url = sprintf('%s/%s/stream/all/getRecent?key=%s',
                    self::API_URI,
                    $this->getResponseType(),
                    $this->getApiKey());
        $this->_send($url);
        if ($this->getResponseType() == 'xml') {
            $results = $this->getResult()->results;
            return $results['array'];
        } else {
            return $this->getResult()->results;
        }
    }



    /**
     * @return array
     */
    public function getMostViewers()
    {
        $url = sprintf('%s/%s/stream/all/getMostViewers?key=%s',
                    self::API_URI,
                    $this->getResponseType(),
                    $this->getApiKey());
        $this->_send($url);
        if ($this->getResponseType() == 'xml') {
            $results = $this->getResult()->results;
            return $results['array'];
        } else {
            return $this->getResult()->results;
        }
    }


    /**
     * This command returns all the current live shows
     * that are new where new is defined as newly created by their owners.
     * The current default value for new is any show less than 1 hour old.
     * This default value may be changed by Ustream.TV at any time.
     * The actual rule used to determine newness will be returned
     *  in the 'msg' portion of the results set when you use this command.
     * Note, the user account may or may not be new.
     * @return array
     */
    public function getRandom()
    {
        $url = sprintf('%s/%s/stream/all/getRandom?key=%s',
                    self::API_URI,
                    $this->getResponseType(),
                    $this->getApiKey());
        $this->_send($url);
        if ($this->getResponseType() == 'xml') {
            $results = $this->getResult()->results;
            return $results['array'];
        } else {
            return $this->getResult()->results;
        }
    }

    public function getAllNew()
    {
        $url = sprintf('%s/%s/stream/all/getAllNew?key=%s',
                    self::API_URI,
                    $this->getResponseType(),
                    $this->getApiKey());
        $this->_send($url);
        if ($this->getResponseType() == 'xml') {
            $results = $this->getResult()->results;
            return $results['array'];
        } else {
            return $this->getResult()->results;
        }
    }

    public function search()
    {
        $search = $this->_getSearchInstance('stream');
        
        return $search;
    }
}