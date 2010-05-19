<?php
/**
 * Services_Wedata
 * 
 * LICENSE
 * 
 * Copyright (c) 2008, sasezaki <sasezaki@gmail.com>
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
 * @package   Services_Wedata
 * @copyright  2008 sasezaki (http://diggin.musicrider.com)
 * @license http://opensource.org/licenses/bsd-license.php New BSD License
 */

require_once 'Services/Wedata/Exception.php';

/**
 * @see HTPP_Request
 */
require_once 'HTTP/Request.php';

/**
 * Handling Wedata Service
 * 
 * @category  Services
 * @package   Services_Wedata
 * @author    Sasezaki <sasezaki at gmail.com>
 * @copyright 2008 Sasezaki
 * @license   http://opensource.org/licenses/bsd-license.php New BSD License
 * @version   0.1.0
 * @see       http://wedata.net/
 */
class Services_Wedata
{
    const API_URL = 'http://wedata.net';
    
    //database
    const PATH_GET_DATABASES = '/databases.json';
    const PATH_GET_DATABASE  = '/databases/%s.json';
    const PATH_CREATE_DATABASE = '/databases';
    const PATH_UPDATE_DATABASE = '/databases/%s';
    const PATH_DELETE_DATABASE = '/databases/%s';
    
    //item
    const PATH_GET_ITEMS = '/databases/%s/items.json';//dbname
    const PATH_GET_ITEM  = '/items/%s.json'; //item id
    const PATH_CREATE_ITEM = '/databases/%s/items'; //dbname
    const PATH_UPDATE_ITEM = '/items/%s'; //item id
    const PATH_DELETE_ITEM = '/items/%s'; //item id

    
    protected static $_itemId;
    
    protected static $_params;

    protected static $_decodetype;

    
    /**
     * Constructs a new Wedata Web Service Client
     *
     * @param array $params parameter acording Wedata
     * @param boolean
     * @return null
     */
    public function __construct(array $params = null, $decodetype = null)
    {
        self::$_params = $params;
        self::$_decodetype = $decodetype;
    }
    
    protected static function _decode($value)
    {
        if (self::$_decodetype === false) {
            //nothig to do
        } else {
            if (self::$_decodetype === null) {
                $value = json_decode($value, true);
            } else {
                $value = json_decode($value, self::$_decodetype);
            }    
        }
        
        return $value;
    }
    
    public static function getParams()
    {
        return self::$_params;
    }
    
    public static function getParam($key)
    {
        return self::$_params[$key];
    }
    
    /**
     * setting parameter
     * 
     * @param array $params
     */
    public static function setParams(array $params)
    {
        foreach ($params as $key => $value){
            self::$_params[strtolower($key)] = $value;
        }
    }
    
    /**
     * adding parameter
     * 
     * @param string
     * @param string
     */
    public static function setParam($key, $value)
    {
        self::$_params[$key] = $value;
    }
    
    /**
     * adding parameter
     * 
     * @param string $key
     * @param string $value
     */
    public static function setParamDatabase($key, $value)
    {
        self::$_params['database'][$key] = $value;
    }
    
    /**
     * adding parameter
     * 
     * @param string
     * @param string
     */
    public static function setApikey($key)
    {
        self::$_params['api_key'] = $key;
    }
        
    /**
     * adding parameter
     * 
     * @param string
     * @param string
     */
    public static function setDatabaseName($databaseName)
    {
        self::$_params['database']['name'] = $databaseName;
    }
    
    /**
     * adding parameter
     * 
     * @param string $itemId
     */
    public static function setItemId($itemId)
    {
        self::$_itemId = $itemId;
    }
    
    /**
     * Handles all requests to a web service
     * 
     * @param string path
     * @param string Prease,using HTTP_Request's define
     * @return mixed
     */
    public static function makeRequest($path, $method, array $params = null)
    {
        $url = new Net_URL(self::API_URL.$path);
        
        $request = new HTTP_Request($url->getURL());
        $request->setMethod($method);
        
        if (!is_null($params)) {            
            if ($method === HTTP_REQUEST_METHOD_GET) {
                foreach ($params as $k => $v) {
                    $request->addQueryString($k, $v);
                }
            } elseif ($method == HTTP_REQUEST_METHOD_POST) {
                foreach ($params as $k => $v) {
                    $request->addPostData($k, $v);
                }
            } else {
                foreach ($params as $k => $v) {
                    $request->addQueryString($k, $v);
                }
            }
        }
        
        if (PEAR::isError($request->sendRequest())) {
            throw new Services_Wedata_Exception($request->getMessage());
        }
        
        //returning response switching by Reqest Method
        if ($method == HTTP_REQUEST_METHOD_GET) {
            return $request->getResponseBody();
        } else {
            $code = $request->getResponseCode();
            $headers = $request->getResponseHeader();
            return array($code, $headers);
        }
    }
    
    public static function getDatabases(array $params = null)
    {
        if ($params) self::setParams($params);
        
        $responseBody = self::makeRequest(self::PATH_GET_DATABASES, HTTP_REQUEST_METHOD_GET, self::$_params);
        
        return self::_decode($responseBody);
    }

    public static function getDatabase($databaseName = null, $page = null)
    {
        if ($databaseName) self::setDatabaseName($databaseName);
        if ($page) self::setParam('page', $page);
        
        $path = sprintf(self::PATH_GET_DATABASE, rawurlencode(self::$_params['database']['name']));
        $responseBody = self::makeRequest($path, HTTP_REQUEST_METHOD_GET, self::$_params);
        
        return self::_decode($responseBody);
    }

    public static function createDatabase(array $params = null)
    {
        if ($params) self::setParams($params);
        
        if(!isset(self::$_params['api_key'])){
            throw new Services_Wedata_Exception('API key is not set ');
        } elseif (!isset(self::$_params['database']['name'])) {
            throw new Services_Wedata_Exception('Database name is not set ');
        } elseif (!isset(self::$_params['database']['required_keys'])) {
            throw new Services_Wedata_Exception('required_keys is not set');
        }
        
        $return = self::makeRequest(self::PATH_CREATE_DATABASE, HTTP_REQUEST_METHOD_POST, self::$_params);
        
        return $return;
    }
    
    
    public static function udpateDatabase($databaseName = null, array $params = null)
    {
        if ($databaseName) self::setDatabaseName($databaseName);
        if ($params) self::setParams($params);
        
        if(!isset(self::$_params['api_key'])){
            throw new Services_Wedata_Exception('API key is not set ');
        } elseif (!isset(self::$_params['database']['required_keys'])) {
            throw new Services_Wedata_Exception('required_keys is not set');
        }

        $path = sprintf(self::PATH_UPDATE_DATABASE, rawurlencode(self::$_params['database']['name']));
        $return = self::makeRequest($path, HTTP_REQUEST_METHOD_PUT, self::$_params);
        
        return $return;
    }
    
    public static function deleteDatabase($databaseName = null, $apiKey = null)
    {
        if ($databaseName) self::setDatabaseName($databaseName);
        if ($apiKey) self::setApikey($apiKey);
        
        if (!isset(self::$_params['database']['name'])) {
            throw new Services_Wedata_Exception('Database name is not set ');
        }
        
        if (isset(self::$_params['api_key'])) {
            $params = array('api_key' => self::$_params['api_key']);
        } else {
            throw new Services_Wedata_Exception('API key is not set ');
        }
        
        $path = sprintf(self::PATH_DELETE_DATABASE, rawurlencode(self::$_params['database']['name']));
        $return = self::makeRequest($path, HTTP_REQUEST_METHOD_DELETE, $params);
        
        return $return;
    }
    
    //////item methods    
    public static function getItems($databaseName = null, $page = null)
    {
        if ($databaseName) self::setDatabaseName($databaseName);
        if ($page) self::setParam('page', $page);
        
        if (isset(self::$_params['page'])) {
            $params = array('page' => self::$_params['page']);
        } else {
            $params = null;
        }
        
        $path = sprintf(self::PATH_GET_ITEMS, rawurlencode(self::$_params['database']['name']));
        $responseBody = self::makeRequest($path, HTTP_REQUEST_METHOD_GET, $params);
        
        return self::_decode($responseBody);
    }

    /**
     * 
     * @param string $itemId
     * @param string $page
     * @return array Decording Result
     */
    public static function getItem($itemId = null, $page = null)
    {
        //@todo if int set as itemid or string searching itemid by name
        //is_integer($item);
        //is_string($item) ;
        
        if ($itemId) self::setItemId($itemId);
        if ($page) self::setParam('page', $page);
        
        if (isset(self::$_params['page'])) {
            $params = array('page' => self::$_params['page']);
        } else {
            $params = null;
        }

        $path = sprintf(self::PATH_GET_ITEM, self::$_itemId);
        $responseBody = self::makeRequest($path, HTTP_REQUEST_METHOD_GET, $params);
        
        return self::_decode($responseBody);
    }
    
    public static function insertItem($databaseName = null, array $params = null)
    {
        if ($databaseName) self::setDatabaseName($databaseName);
        if ($params) self::setParams($params);
        
        $path = sprintf(self::PATH_CREATE_ITEM, rawurlencode(self::$_params['database']['name']));
        $return = self::makeRequest($path, HTTP_REQUEST_METHOD_POST, self::$_params);
        
        return $return;
    }
    
    public static function updateItem($itemId = null, array $params = null)
    {
        if ($itemId) self::setItemId($itemId);
        if ($params) self::setParams($params);
        
        if (!isset(self::$_params['api_key'])) {
            throw new Services_Wedata_Exception('API key is not set ');
        }
        
        $path = sprintf(self::PATH_UPDATE_ITEM, self::$_itemId);
        $return = self::makeRequest($path, HTTP_REQUEST_METHOD_PUT, self::$_params);
        
        return $return;
    }
    
    public static function deleteItem($itemId = null, $apiKey = null)
    {
        if ($itemId) self::setItemId($itemId);
        if ($apiKey) self::setApikey($apiKey);
        
        if (isset(self::$_params['api_key'])) {
            $params = array('api_key' => self::$_params['api_key']);
        } else {
            throw new Services_Wedata_Exception('API key is not set ');
        }
        
        $path = sprintf(self::PATH_DELETE_ITEM, self::$_itemId);
        $return = self::makeRequest($path, HTTP_REQUEST_METHOD_DELETE, $params);
        
        return $return;
    }
}
?>
