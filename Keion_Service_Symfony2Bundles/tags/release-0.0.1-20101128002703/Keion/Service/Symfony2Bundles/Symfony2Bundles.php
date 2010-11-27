<?php

/**
 * Keion - Azunyan pero-pero Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license.
 * It is also available through the world-wide-web at this URL:
 * http://diggin.musicrider.com/LICENSE
 * 
 * @category   Keion
 * @package    Keion_Service
 * @subpackage Symfony2Bundles
 * @copyright  2010 sasezaki (http://diggin.musicrider.com)
 * @license    http://diggin.musicrider.com/LICENSE     New BSD License
 */

namespace Keion\Service\Symfony2Bundles;

use \Zend_Http_Client as HTTPClient;
use \Zend_Json as Json;

/**
 * Symfony2Bundles
 *
 * @see http://symfony2bundles.org/api
 */
class Symfony2Bundles
{
    const API_URL = 'http://symfony2bundles.org';
    const PATH_BUNDLES    = '/bundle/%s.%s';
    const PATH_BUNDLE     = '/%s/%s.%s';
    const PATH_PROJECTS   = '/project/%s.%s';
    const PATH_PROJECT    = '/%s/%s.%s';
    const PATH_SEARCH     = '/search.%s?q=%s';
    const PATH_DEVELOPERS = '/developer/%s.%s';
    const PATH_DEVELOPER  = '/%s.$s';
    const PATH_DEVELOPER_BUNDLES   = '/%s/bundles.%s';
    const PATH_DEVELOPER_PROJECTS = '/%s/projects.%s'; 

    /**
     * \Zend_Http_Client Object
     *
     * @var \Zend_Http_Client
     */
    protected $_httpClient;

    private $_jsonDecodeType = 0; //Zend_Json::TYPE_OBJECT
    private $_handleResponse;

    private $_format = 'json';

    /**
     * Constructs a new Symfony2Bundles Web Service Client
     *
     * @param string format
     */
    public function __construct($format = 'json')
    {
        $this->setDefaultFormat($format);
    }
 
    /**
     * Set \Zend_Http_Client
     *
     * @param \Zend_Http_Client $client
     */   
    public function setHttpClient(HTTPClient $client)
    {
        $this->_httpClient = $client;
    }

    /**
     * Get Http Client - lazy load
     *
     * @return \Zend_Http_Client
     */
    public function getHttpClient()
    {
        if (!$this->_httpClient instanceof HTTPClient) {
            $this->_httpClient = new HTTPClient();
        }

        return $this->_httpClient;
    }

    protected function makeRequest($path, $params)
    {
        $client = $this->getHttpClient();
        $path = call_user_func_array('sprintf', $args = func_get_args());

        $client->setUri(self::API_URL.$path);
        $response = $client->request();

        return $response;
    }

    protected function handleResponse($response, $params, $point)
    {
        if (!is_callable($callback = $this->_handleResponse)) {
            $callback = array($this, 'defaultHandelResponse');
        }

        return call_user_func_array($callback, array($response, $params, $point));
    }

    protected function setHandleResponse($callback)
    {
        $this->_handleResponse = $callback;
    }

    protected function defaultHandelResponse($response, $params)
    {
        if ('json' === $params['format']) {
            return Json::decode($response->getBody(), $this->getJsonDecodeType());
        } else {
            return $response->getBody();
        }
    }

    public function setJsonDecodeType($type)
    {
        $this->_jsonDecodeType = $type;
    }

    public function getJsonDecodeType()
    {
        return $this->_jsonDecodeType;
    }

    public function setDefaultFormat($format)
    {
        $this->_format = $this->checkFormat($format);
    }

    protected function checkFormat($format)
    {
        if (!in_array($format = strtolower($format), array('json', 'jsonp', 'js'))) {
            throw new Exception\InvalidArgumentException();
        }
        return  ('jsonp' === $format) ? 'js' : $format;
    }
    
    public function getDefaultFormat()
    {
        return $this->_format;
    }

    protected function format($format = null)
    {
        return ($format) ? $this->checkFormat($format) : $this->getDefaultFormat();
    }

    public function getBundles($sort = 'name', $format = null)
    {
        $response = $this->makeRequest(self::PATH_BUNDLES, $sort, $format = $this->format($format));
        return $this->handleResponse($response, compact('sort', 'format'), __METHOD__);
    }

    public function getBundle($username, $name, $format = null)
    {
        $response = $this->makeRequest(self::PATH_BUNDLE, $username, $name, $format = $this->format($format));
        return $this->handleResponse($response, compact('username', 'name', 'format'), __METHOD__);
    }

    public function getProjects($sort, $format = null)
    {
        $response = $this->makeRequest(self::PATH_PROJECTS, $sort, $format = $this->format($format));
        return $this->handleResponse($response, compact('sort', 'format'), __METHOD__);
    }

    public function getProject($username, $name, $format = null)
    {
        $response = $this->makeRequest(self::PATH_PROJECT, $username, $name, $format =  $this->format($format));
        return $this->handleResponse($response, compact('username', 'name', 'format'), __METHOD__);
    }

    public function search($query, $format = null)
    {
        $response = $this->makeRequest(self::PATH_SEARCH, $format = $this->format($format), $query);
        return $this->handleResponse($response, compact('format', 'query'), __METHOD__);
    }

    public function getDevelopers($sort, $format = null)
    {
        $response = $this->makeRequest(self::PATH_DEVELOPERS, $sort, $format =  $this->format($format));
        return $this->handleResponse($response, compact('sort', 'format'), __METHOD__);
    }

    public function getDeveloper($name, $format = null)
    {
        $response = $this->makeRequest(self::PATH_DEVELOPER, $name, $format = $this->format($format));
        return $this->handleResponse($response, compact('name', 'format'), __METHOD__);
    }

    public function getDeveloperBundles($name, $format = null)
    {
        $response = $this->makeRequest(self::PATH_DEVELOPER_BUNDLES, $name, $format = $this->format($format));
        return $this->handleResponse($response, compact('sort', 'format'), __METHOD__);
    }

    public function getDeveloperProjects($name, $format = null)
    {
        $response = $this->makeRequest(self::PATH_DEVELOPER_PROJECTS, $name, $format =  $this->format($format));
        return $this->handleResponse($response, compact('name', 'format'), __METHOD__);
    }

    public static function __callStatic($method, $args)
    {
        if ('static' !== substr($method, 0, 6)) {
            throw new Exception\InvalidArgumentException($method);
        } else {
            $function = lcfirst(substr($method, 6));
        }

        $self = new self;
        if ($args[count($args) -1] instanceof HTTPClient) {
            $client = array_pop($args);
            $self->setHttpClient($client);
        }

        return call_user_func_array(array($self, $function), $args);       
    }
}
