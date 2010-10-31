<?php
/**
 * Main Services_SBM_Info class
 *
 * PHP version 5.2
 *
 * Copyright (c) 2010 Hiroshi Hoaki, All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Hiroshi Hoaki nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category   Services
 * @package    Services_SBM_Info
 * @version    SVN: $Id$
 * @author     Hiroshi Hoaki <rewish.org@gmail.com>
 * @copyright  2010 Hiroshi Hoaki
 * @license    http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @link       http://openpear.org/package/Services_SBM_Info
 */

require_once 'Services/SBM/Info/Exception.php';

/**
 * Main Services_SBM_Info class
 *
 * @category   Services
 * @package    Services_SBM_Info
 * @version    SVN: $Id$
 * @author     Hiroshi Hoaki <rewish.org@gmail.com>
 * @copyright  2010 Hiroshi Hoaki
 * @license    http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @link       http://openpear.org/package/Services_SBM_Info
 */
class Services_SBM_Info
{
    /**
     * Services_SBM_Info version
     */
    const VERSION = '0.3.0-beta';

    /**
     * Target URL
     * @var string
     */
    protected $_url;

    /**
     * Page title
     * @var string
     */
    protected $_title;

    /**
     * Enable services
     * @var array
     */
    protected $_services;

    /**
     * Error log file path
     * @var string
     */
    protected $_errorLog = null;

    /**
     * Execute failed services
     * @var array
     */
    protected $_failedServices;

    /**
     * Object pool for SBM Service
     * @var array
     */
    protected $_objects;

    /**
     * Constructor
     *
     * @param string $url Target URL
     * @param string $title Page title
     * @param string|array $services "," split SBM Service name | SBM Service name array
     */
    public function __construct($url = null, $title = null, $services = 'hatena,delicious')
    {
        $this->setUrl($url)
             ->setTitle($title)
             ->setServices($services);
    }

    /**
     * Call SBM Service class method
     *
     * @param  string $method method
     * @param  mixed  $args arguments
     * @return mixed
     */
    public function __call($method, $args)
    {
        if (!empty($args) && !empty($args[0])) {
            return call_user_func_array(array($this, 'factory'), $args)->{$method}();
        }
        $ret = array();
        foreach ($this->_services as $serviceName) {
            $serviceName = $this->camelize($serviceName);
            $Service = $this->factory($serviceName);
            $ret[$serviceName] = $Service->$method();
        }
        return $ret;
    }

    /**
     * Object factory for SBM Service
     *
     * @param  string $serviceName SBM Service name
     * @return object
     */
    public function factory($serviceName)
    {
        $serviceName = $this->camelize($serviceName);
        if (isset($this->_objects[$serviceName])) {
            return $this->_objects[$serviceName];
        }
        require_once implode('/', explode('_', __CLASS__)) . '/' . $serviceName . '.php';
        $class = __CLASS__ . '_' . $serviceName;
        $this->_objects[$serviceName] = new $class($this->_url, $this->_title);
        return $this->_objects[$serviceName];
    }

    /**
     * Set target URL
     *
     * @param  string $url Target URL
     * @return object $this Services_SBM_Info object
     */
    public function setUrl($url)
    {
        $this->_url = $url;
        return $this;
    }

    /**
     * Set page title
     *
     * @param  string $title Page title
     * @return object $this Services_SBM_Info object
     */
    public function setTitle($title)
    {
        $this->_title = $title;
        return $this;
    }

    /**
     * Set services
     *
     * @param  string|array $services "," split SBM Service name | SBM Service name array
     * @return object $this Services_SBM_Info object
     */
    public function setServices($services)
    {
        if (is_string($services)) {
            $services = explode(',', $services);
        }
        $this->_services = $services;
        return $this;
    }

    /**
     * Set fetch function
     *
     * @param  string|array $func User function
     * @return object $this Services_SBM_Info object
     */
    public function setFetchFunction($func)
    {
        foreach ($this->_services as $serviceName) {
            $this->factory($serviceName)->setFetchFunction($func);
        }
        return $this;
    }

    /**
     * Set convert object function
     *
     * @param  string|array $func User function
     * @return object $this Services_SBM_Info object
     */
    public function setToObjectFunction($func)
    {
        foreach ($this->_services as $serviceName) {
            $this->factory($serviceName)->setToObjectFunction($func);
        }
        return $this;
    }

    /**
     * Set proxy
     *
     * @param string  $host Host | IP
     * @param integer $port Port
     * @return object Services_SBM_Info object
     */
    public function setProxy($host, $port)
    {
        foreach ($this->_services as $serviceName) {
            $this->factory($serviceName)->setProxy($host, $port);
        }
        return $this;
    }

    /**
     * Set error log
     *
     * @param string $errorLog Log file path
     * @return object Services_SBM_Info object
     */
    public function setErrorLog($errorLog)
    {
        $this->_errorLog = $errorLog;
        return $this;
    }

    /**
     * Execute
     *
     * @return void
     */
    public function execute()
    {
        $this->_failedServices = array();
        foreach ($this->_services as $serviceName) {
            $Service = $this->factory($serviceName);
            try {
                $Service->setUrl($this->_url)
                        ->setTitle($this->_title)
                        ->execute();
            } catch(Services_SBM_Info_Exception $e) {
                if ($this->_errorLog) {
                    error_log($e->getMessage(), 3, $this->_errorLog);
                }
                $this->_failedServices[] = $this->camelize($serviceName);
            }
        }
    }

    /**
     * All SBM info to Array
     *
     * @param  boolean $getComments Comments also get
     * @return array All SBM info
     */
    public function getAll($getComments = false)
    {
        $keys = array('count', 'unit', 'rank', 'entry_url', 'add_url');
        $ret = array();
        foreach ($this->_services as $serviceName) {
            $serviceName = $this->camelize($serviceName);
            $Service = $this->factory($serviceName);
            $Service->setUrl($this->_url)
                    ->setTitle($this->_title);
            $ret[$serviceName] = array();
            foreach ($keys as $key) {
                $method = 'get' . $this->camelize($key, '_');
                $ret[$serviceName][$key] = $Service->$method();
            }
            if ($getComments) {
                $ret[$serviceName]['comments'] = $Service->getComments();
            }
        }
        return $ret;
    }

    /**
     * Get execute failed services
     *
     * @return array failed services
     */
    public function getFailedServices()
    {
        return $this->_failedServices;
    }

    /**
     * String to Camel case
     *
     * @param  string $str
     * @param  string $separator
     * @return string
     */
    public function camelize($str, $separator = ' ')
    {
        return implode('', array_map(array($this, '_camelize'), explode($separator, $str)));
    }

    /**
     * array_map callback for camelize
     *
     * @param  string $str
     * @return string
     */
    protected function _camelize($str)
    {
        return ucfirst(strtolower($str));
    }
}