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
    protected $_command;
    protected $_scopeAndSorting;
    protected $_params = array();
    protected $_allowedCommands = array('user', 'channel', 'stream', 'video');

    public function command($command)
    {
        if (!in_array($command, $this->_allowedCommands)) {
            throw new Services_Ustream_Exception('Invalid command.');
        } else {
            $this->_command = $command;
        }

        return $this;
    }

    public function scope($scope)
    {
        $this->_scopeAndSorting = $scope;
        return $this;
    }
    
    public function uid($uid)
    {
        $this->_scopeAndSorting = $uid;
        return $this;
    }

    /**
     * @param bool $flag
     * @return Services_Ustream_Search
     */
    public function newest($flag = true)
    {
        $this->_scopeAndSorting =
            ($flag) ? 'newest' : '!newest';
        return $this;
    }

    /**
     * @param bool $flag
     * @return Services_Ustream_Search
     */
    public function recent($flag = true)
    {
        $this->_scopeAndSorting =
            ($flag) ? 'recent' : '!recent';
        return $this;
    }

    /**
     * @return Services_Ustream_Search
     */
    public function all()
    {
        $this->_scopeAndSorting = 'all';
        return $this;
    }

    /**
     * @return Services_Ustream_Search
     */
    public function popular()
    {
        $this->_scopeAndSorting = 'popular';
        return $this;
    }

    /**
     * @return Services_Ustream_Search
     */
    public function live()
    {
        $this->_scopeAndSorting = 'live';
        return $this;
    }

    /**
     *
     * @param string $name
     * @return Services_Ustream_Search
     */
    public function where($name)
    {
        $this->_params[0] = $name;
        return $this;
    }

    /**
     *
     * @param string $value
     * @return Services_Ustream_Search
     */
    public function like($value)
    {
        $this->_params[1] = 'like';
        $this->_params[2] = $value;
        return $this;
    }

    /**
     *
     * @param string $value
     * @return Services_Ustream_Search
     */
    public function eq($value)
    {
        $this->_params[1] = 'eq';
        $this->_params[2] = $value;
        return $this;
    }

    /**
     *
     * @param string $value
     * @return Services_Ustream_Search
     */
    public function lt($value)
    {
        $this->_params[1] = 'lt';
        $this->_params[2] = $value;
        return $this;
    }

    /**
     *
     * @param string $value
     * @return Services_Ustream_Search
     */
    public function gt($value)
    {
        $this->_params[1] = 'gt';
        $this->_params[2] = $value;
        return $this;
    }

    /**
     *
     * @return array
     */
    public function query()
    {
        $url = sprintf('%s/%s/%s/%s/search/%s?key=%s',
                self::API_URI,
                $this->getResponseType(),
                $this->_command,
                $this->_scopeAndSorting,
                implode(':', $this->_params),
                $this->getApiKey());
        $this->_send($url);

        if ($this->getResponseType() == 'xml') {
            $results = $this->getResult()->results;
            return $results['array'];
        } else {
            return $this->getResult()->results;
        }
    }

}
