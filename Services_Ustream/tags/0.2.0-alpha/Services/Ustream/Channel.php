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
    protected $_subject = 'channel';

    public function getInfo($uid)
    {
        $this->setParam('uid', $uid);
        $this->setParam('command', 'getInfo');
        return $this->_sendRequest();
    }

    public function getValueOf($uid, $property)
    {
        $_properties = array('id', 'user', 'title', 'description', 'urlTitleName',
                             'url', 'status', 'createdAt', 'lastStreamedAt',
                             'photoUrl', 'protected', 'rating', 'embedTag',
                             'embedTagSourceUrl', 'hasTags', 'numberOf', 'tags',
                             'comments');
        if (in_array($property, $_properties)) {
            $this->setParam('uid', $uid);
            $this->setParam('command', 'getValueOf');
            $this->setParam('params', $property);
            return $this->_sendRequest();
        } else {
            throw new Services_Ustream_Exception('Invalid property.');
        }
    }

    public function getId()
    {
        throw new Exception(__FUNCTION__ . ' is disable now.');
    }

    public function getEmbedTag($uid)
    {
        $this->setParam('uid', $uid);
        $this->setParam('command', 'getEmbedTag');
        return $this->_sendRequest();
    }

    public function getCustomEmbedTag($uid, $opts = array())
    {
        $params = '';
        foreach ($opts as $key => $val) {
            $params .= $key . ':' . $val . ';';
        }
        $this->setParam('uid', $uid);
        $this->setParam('command', 'getCustomEmbedTag');
        if ($params) $this->setParam('params', $params);
        return $this->_sendRequest();
    }

    public function listAllChannels($uid)
    {
        throw new Exception(__FUNCTION__ . 'is disable now.');
    }

    public function getComments($uid)
    {
        throw new Exception(__FUNCTION__ . 'is disable now.');
    }

    public function getTags($uid)
    {
        throw new Exception(__FUNCTION__ . 'is disable now.');
    }
}

