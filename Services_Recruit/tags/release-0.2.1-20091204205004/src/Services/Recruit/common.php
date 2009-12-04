<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
/**
* @category  Web Services
* @package   Services_Recruit
* @author    Tadashi Jokagi <elf@php.net>
* @copyright 2008 John Downey
* @license   http://www.freebsd.org/copyright/freebsd-license.html 2 Clause BSD License
* @version   CVS: $Id$
* @link      http://pear.php.net/package/Services_Recruit/
* @filesource
*/

define('SERVICES_RECRUIT_RESULT_XML', 'xml');
define('SERVICES_RECRUIT_RESULT_JSON', 'json');
define('SERVICES_RECRUIT_RESULT_JSONS', 'jsonp');

/**
* Uses PEAR class for error management.
*/
require_once 'PEAR.php';

/**
* Uses HTTP_Request class to send and receive data from Recruit web servers.
*/
require_once 'HTTP/Request.php';

/**
* Uses XML_Unserializer class to parse data received from Recruit.
*/
require_once 'XML/Unserializer.php';

/**
* Class for accessing and retrieving information from Recruit's Web Services.
*
* @package Services_Recruit
* @author  Tadashi Jokagi <elf@php.net>
* @access  public
* @version Alpha: 0.1.0
* @uses    PEAR
* @uses    HTTP_Request
* @uses    XML_Unserializer
*/
class Services_Recruit_common
{
    /**
    *
    * @access private
    * @var    string $_serviceName
    */
    var $_serviceName = null;

    /**
    *
    * @access private
    * @var    string $_serviceApiVersion
    */
    var $_serviceApiVersion = null;

    /**
    * The developers token used when quering Recruit servers.
    *
    * @access private
    * @var    string $_key
    */
    var $_serviceApikey = null;
    
    /**
    *
    * @access private
    * @var    string $_resultFormat
    */
    var $_resultFormat = null;

    /**
    *
    * @access private
    * @var    string $_resultJsonpCallback
    */
    var $_resultJsonpCallback = null;

    /**
    * The base url used to build the query for the Recruit servers.
    *
    * @access private
    * @var    string $_baseUrl
    */
    var $_baseUrl = null;

    /**
    * Constructor
    *
    * @access public
    * @param  string $key
    * @param  string $serviceName
    * @param  string $serviceApiVersion
    * @see    setBaseUrl
    */
    function Services_Recruit_common($key, $serviceName, $serviceApiVersion = null) {
        $this->setBaseUrl('http://webservice.recruit.co.jp/');
        $this->setServiceApiKey($key);
        $this->setServiceName($serviceName);
        $this->setServiceApiVersion($serviceApiVersion);
        $this->setResultFormat(SERVICES_RECRUIT_RESULT_XML);
    }

    /**
    * Retrieves the current version of this classes API.
    *
    * All major versions are backwards compatible with older version of the same
    * version number. Such as 1.5 would work for a script written to use 1.0.
    * However on the filp side a script that needs 1.5 would not work with
    * API version 1.0.
    *
    * @access public
    * @static
    * @return string the API version
    */
    function getApiVersion() {
        return '1.0';
    }

    /**
    * Retrieves the currently set Developer token.
    * 
    * To use Recruit's Web Services you need a developer's token. Visit
    * {@link http://www.amazon.com/webservices} and read their license
    * agreement to recieve a free token.
    *
    * @access public
    * @return string the currently set Developer token
    * @see    setToken()
    */
    function getServiceApiKey() {
        return $this->_serviceApiKey;
    }

    /**
    * Sets the Developer token to use when quering Recruit's Web Services.
    *
    * @access public
    * @param  string $token your Developer token
    * @return void
    * @see    getToken()
    */
    function setServiceApiKey($key) {
        $this->_serviceApiKey = $key;
    }

    /**
    * Retrieves the currently set base url.
    *
    * @access public
    * @return string the currently set base url
    * @see    setBaseUrl()
    */
    function getBaseUrl() {
        return $this->_baseUrl;
    }

    /**
    * Sets the base url used when making a query to Recruit.com.
    *
    * @access public
    * @param  string $baseurl the base url to use
    * @return void
    * @see    getBaseUrl()
    */
    function setBaseUrl($baseurl) {
        $this->_baseUrl = $baseurl;
    }

    function getServiceName()
    {
        return $this->_serviceName;
    }

    function setServiceName($name)
    {
        $this->_serviceName = strval($name);
    }

    function getServiceApiVersion()
    {
        return $this->_serviceApiVersion;
    }

    function setServiceApiVersion($version)
    {
        $this->_serviceApiVersion = strval($version);
    }

    function getResultFormat()
    {
        return array('format'=>$this->_resultFormat, 'callback'=>$_resultJsonpCallback);
    }

    function setResultFormat($format, $callback = null)
    {
        $this->_resultFormat = $format;
        $this->_resultJsonpCallback = $callback;
    }

    function &_sendRequest($method, $params = array(), $start = null, $count = null) {
        $api_key = $this->getServiceApiKey();
        if (is_null($api_key)) {
            return PEAR::raiseError('API key have not been set.');
        }

        $params['format'] = $this->getResultFormat();

        if (!is_null($start)) {
            $params['start'] = $start;
        }

        if (!is_null($count)) {
            $params['count'] = $count;
        }

        $result_format = $this->getResultFormat();
        switch ($result_format['format']) {
        case SERVICES_RECRUIT_RESULT_XML:
        case SERVICES_RECRUIT_RESULT_JSON:
            $params['format'] = $result_format['format'];
            break;

        case SERVICES_RECRUIT_RESULT_JSONP:
            $params['format'] = $result_format['format'];
            $params['callback'] = $result_format['callback'];
        }

        $queries = array();
        foreach ($params as $key => $value) {
            if(!is_null($value)) {
                $queries[] = $key . '=' . urlencode($value);
            }
        }
        $query = implode('&', $queries);
        $url = sprintf('%s%s/%s/v%s/?key=%s&%s',
                       $this->getBaseUrl(),
                       $this->getServiceName(),
                       $method,
                       $this->getServiceApiVersion(),
                       $api_key,
                       $query);

        // Open up our HTTP_Request and set our User-Agent field then send the
        // request for the URL.
        $http = &new HTTP_Request($url);
        $http->addHeader('User-Agent', 'Services_Service/' . $this->getApiVersion());
        $http->sendRequest();
        
        // Retrieve the result and check that its HTTP 200 Ok. Otherwise raise
        // an error.
        if ($http->getResponseCode() != 200) {
            return PEAR::raiseError('Service return HTTP ' . $http->getResponseCode());
        }
        $result = $http->getResponseBody();
        
        // Start up the XML_Unserializer and feed it the data received from
        // Service.com
        $xml = &new XML_Unserializer(array('complexType' => 'object', 'keyAttribute' => 'url'));
        $xml->unserialize($result, false);
        $data = $xml->getUnserializedData();
        
        if (isset($data->ErrorMsg)) {
            return PEAR::raiseError($data->ErrorMsg);
        }
        
        // Prepare the data to be sent to _processPage
        $data  = get_object_vars($data);

        return $data;
    }
}
?>
