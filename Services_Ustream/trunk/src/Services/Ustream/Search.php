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
 * Search class for Services_Ustream
 *
 * @category  Services
 * @package   Services_Ustream
 * @author    Kimiaki Makino <makino@gagne.jp>
 * @copyright 2009 Kimiaki Makino
 * @license   http://opensource.org/licenses/bsd-license.php New BSD License
 * @link      http://openpear.otg/package/Services_Ustream
 */
class Services_Ustream_Search extends Services_Ustream_Abstract
{
    protected $subject = 'search';
    protected $command;
    protected $scopeOrSorting;
    protected $searchParams = array();

    /**
     * Set command
     *
     * @param string $command Command name
     *
     * @return object
     * @throws Services_Ustream_Exception
     */
    public function command($command)
    {
        $commands = array('user', 'channel', 'stream', 'video');
        if (in_array($command, $commands)) {
            $this->command = $command;
        } else {
            throw new Services_Ustream_Exception('Invalid command.');
        }
        return $this;
    }

    /**
     * Set scope
     *
     * @param string $scope scope
     *
     * @return object
     */
    public function scope($scope)
    {
        $this->scopeOrSorting = $scope;
        return $this;
    }

    /**
     * Set uid
     *
     * @param string $uid UID
     *
     * @return object
     */
    public function uid($uid)
    {
        $this->scopeOrSorting = $uid;
        return $this;
    }

    /**
     * newest
     *
     * @param bool $flag newst or not
     *
     * @return object
     */
    public function newest($flag = true)
    {
        $this->scopeOrSorting = ($flag) ? 'newest' : '!newest';
        
        return $this;
    }

    /**
     * recent
     *
     * @param bool $flag recent or not
     *
     * @return object
     */
    public function recent($flag = true)
    {
        $this->scopeOrSorting = ($flag) ? 'recent' : '!recent';
        return $this;
    }

    /**
     * all
     *
     * @return Services_Ustream_Search
     */
    public function all()
    {
        $this->scopeOrSorting = 'all';
        return $this;
    }

    /**
     * live
     *
     * @return Services_Ustream_Search
     */
    public function live()
    {
        $this->scopeOrSorting = 'live';
        return $this;
    }

    /**
     * popular
     *
     * @return Services_Ustream_Search
     */
    public function popular()
    {
        $this->scopeOrSorting = 'popular';
        return $this;
    }

    /**
     * where
     *
     * @param string $key Search key
     *
     * @return Services_Ustream_Search
     */
    public function where($key)
    {
        $this->searchParams[0] = $key;
        return $this;
    }

    /**
     * like
     *
     * @param string $value value
     *
     * @return Services_Ustream_Search
     */
    public function like($value)
    {
        $this->searchParams[1] = 'like:' . $value;
        return $this;
    }

    /**
     * eq
     *
     * @param string $value value
     *
     * @return Services_Ustream_Search
     */
    public function eq($value)
    {
        $this->searchParams[1] = 'eq:' . $value;
        return $this;
    }

    /**
     * lt
     *
     * @param string $value value
     *
     * @return Services_Ustream_Search
     */
    public function lt($value)
    {
        $this->searchParams[1] = 'lt:' . $value;
        return $this;
    }

    /**
     * gt
     *
     * @param string $value value
     *
     * @return Services_Ustream_Search
     */
    public function gt($value)
    {
        $this->searchParams[1] = 'gt:' . $value;
        return $this;
    }

    /**
     * Send request and get result
     *
     * @return mixed Services_Ustream_Result | string
     * @throws Services_Ustream_Exception
     */
    public function query()
    {
        $params = implode(':', $this->searchParams);
        $url = sprintf(
            '%s/%s/%s/%s/%s/%s?key=%s',
            $this->baseUrl,
            $this->responseType,
            $this->command,
            $this->scopeOrSorting,
            $this->subject,
            $params,
            $this->apiKey
        );
        echo $url;
        
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



}
