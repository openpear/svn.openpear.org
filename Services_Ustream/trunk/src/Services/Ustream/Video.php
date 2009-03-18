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
 * Uses Services_Ustream_Abstract
 */
require_once 'Services/Ustream/Abstract.php';

/**
 * Video class for Services_Ustream
 *
 * @category  Services
 * @package   Services_Ustream
 * @author    Kimiaki Makino <makino@gagne.jp>
 * @copyright 2009 Kimiaki Makino
 * @license   http://opensource.org/licenses/bsd-license.php New BSD License
 * @link      http://openpear.otg/package/Services_Ustream
 */
class Services_Ustream_Video extends Services_Ustream_Abstract
{
    protected $subject = 'video';

    /**
     * getInfo
     *
     * @param integer $uid Video ID.
     *
     * @return string|Services_Ustream_Result
     */
    public function getInfo($uid)
    {
        $this->setParam('uid', $uid);
        $this->setParam('command', 'getInfo');
        return $this->sendRequest();
    }

    /**
     * getValueOf
     *
     * @param integer $uid      Video ID.
     * @param string  $property property
     * 
     * @return string|Services_Ustream_Result
     * @throws Services_Ustream_Exception
     */
    public function getValueOf($uid, $property)
    {
        $_properties = array('id', 'user', 'title', 'description', 'createdAt',
                             'url', 'lengthInSecond', 'imageUrl', 'rating', 
                             'embedTag', 'embedTagSourceUrl', 'hasTags',
                             'numberOf', 'tags', 'sourceChannel');

        if (in_array($property, $_properties)) {
            $this->setParam('uid', $uid);
            $this->setParam('command', 'getValueOf');
            $this->setParam('params', $property);
            return $this->sendRequest();
        } else {
            throw new Services_Ustream_Exception('Invalid property.');
        }
    }

    /**
     * getId
     *
     * @param string $videoUrl Video URL
     *
     * @return string|Services_Ustream_Result
     */
    public function getId($videoUrl)
    {
        $this->setParam('uid', $videoUrl);
        $this->setParam('command', 'getValueOf');
        $this->setParam('params', 'id');
        return $this->sendRequest();
    }

    /**
     * getEmbedTag
     *
     * @param integer $uid Video ID.
     *
     * @return string|Services_Ustream_Result
     */
    public function getEmbedTag($uid)
    {
        $this->setParam('uid', $uid);
        $this->setParam('command', 'getEmbedTag');
        return $this->sendRequest();
    }

    /**
     * listAllVideos
     *
     * @param integer $uid Video ID.
     *
     * @return string|Services_Ustream_Result
     */
    public function listAllVideos($uid)
    {
        $this->setParam('uid', $uid);
        $this->setParam('command', 'listAllVideos');
        return $this->sendRequest();
    }

    /**
     * getComments
     *
     * @param integer $uid Video ID.
     *
     * @return string|Services_Ustream_Result
     */
    public function getComments($uid)
    {
        $this->setParam('uid', $uid);
        $this->setParam('command', 'getComments');
        return $this->sendRequest();
    }

    /**
     * getTags
     *
     * @param integer $uid Video ID.
     *
     * @return string|Services_Ustream_Result
     */
    public function getTags($uid)
    {
        $this->setParam('uid', $uid);
        $this->setParam('command', 'getTags');
        return $this->sendRequest();
    }
}

