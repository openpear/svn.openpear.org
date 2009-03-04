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

class Services_Ustream_Result
{
    protected $_results;
    protected $_msg;
    protected $_error;
    protected $_processTime;
    protected $_version;
    protected $_responseType;

    public function __construct($response, $responseType)
    {
        switch ($responseType) {
            case 'xml':
                $this->_fromXml($response);
                break;
            case 'php':
                $this->_fromPhp($response);
                break;
        }

        if ($this->_error) {
            throw new Services_Ustream_Exception($this->_msg);
        }
    }

    public function getResults()
    {
        return $this->_results;
    }

    public function getProcessTime()
    {
        return $this->_processTime;
    }

    public function getVersion()
    {
        return $this->_version;
    }

    public function __get($name)
    {
        if (isset($this->_results[$name])) {
            return $this->_results[$name];
        }
        return null;
    }
    
    protected function _fromXml($response)
    {
        require_once 'XML/Unserializer.php';
        $xml = new XML_Unserializer;
        $xml->unserialize($response);
        $results = $xml->getUnserializedData();

        $this->_results = $results['results'];
        $this->_msg = $results['msg'];
        $this->_error = $results['error'];
        $this->_processTime = $results['processTime'];
        $this->_version = $results['version'];
    }

    protected function _fromPhp($response)
    {
        $results = unserialize($response);
        $this->_results = $results['results'];
        $this->_msg = $results['msg'];
        $this->_error = $results['error'];
        $this->_processTime = $results['processTime'];
        $this->_version = $results['version'];
    }
}

