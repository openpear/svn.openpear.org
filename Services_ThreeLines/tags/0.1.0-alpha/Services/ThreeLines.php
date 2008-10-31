<?php

/**
 * Service class for 3lines.info
 *
 * PHP version 5
 *
 * LICENSE: This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_01.txt.  If you did not receive a copy
 * the PHP License and are unable to obtain it through the web,
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category  Services
 * @package   Services_ThreeLines
 * @author    Sotaro KARASAWA <sotaro.k@gmail.com>
 * @copyright 2008 Sotaro KARASAWA
 * @license   http://www.php.net/license/3_01.txt The PHP License, version 3.01
 * @version   0.0.1
 * @link      http://openpear.org/package/Services_ThreeLines
 */


require_once 'HTTP/Request.php';

/**
 * Service class for 3lines.info
 *
 * @category  Services
 * @package   Services_ThreeLines
 * @author    Sotaro KARASAWA <sotaro.k@gmail.com>
 * @copyright 2008 Sotaro KARASAWA
 * @license   http://www.php.net/license/3_01.txt The PHP License, version 3.01
 * @version   0.0.1
 * @link      http://openpear.org/package/Services_ThreeLines
 */
class Services_ThreeLines
{
    /* Base URL of 3lines.info
     */
    const URL_BASE = "http://www.3lines.info/";

    /* summary path
     */
    const URL_SUMMARY = "summary.js";

    protected $_browser;

    protected $_url;

    protected $_body;

    protected $_json;

    protected $_options = array();

    public $title;

    public $summaries;

    public $summaryString;

    /**
     * constructor
     *
     * @param   string  $url    url
     * @param   array   $options options
     * @access  public
     * @return  object  Services_ThreeLines
     */
    public function __construct ($url, $options = array())
    {
        $this->_browser = new HTTP_Request(self::URL_BASE . self::URL_SUMMARY);
        $this->setUrl($url);
    }

    /**
     * set url that want to summarize
     *
     * @param   string  $url    url
     * @access  public
     * @return  object  Services_ThreeLines
     */
    public function setUrl($url)
    {
        $this->_url = $url;
        $this->_browser->addQueryString('url', $this->_url);

        return $this;
    }

    /**
     * send request to service
     *
     * @access  public
     * @return  object  Services_ThreeLines
     */
    public function request ()
    {
        $url = $this->_browser->getUrl();
        if (empty($url)) {
            throw new Exception ("Cannot send request without set url" . __METHOD__);
        }

        $res = $this->_browser->sendRequest();

        if (PEAR::isError($res)) {
            throw new Exception ("Request Failure at " . __METHOD__);
        }

        $header = $this->_browser->getResponseHeader();
        
        if ($header == "500") {
            throw new Exception ("Server Error at " . __METHOD__);
        }

        $this->_body = $this->_browser->getResponseBody();

        return $this;
    }

    /**
     * parsing json response
     *
     * @access  public
     * @return  object  Services_ThreeLines
     */
    public function parse ()
    {
        $this->_json = json_decode($this->_body);

        if (!($this->_json instanceof StdClass)) {
            throw new Exception ("Parse Failure at " . __METHOD__);
        }

        $this->summaries = $this->_json->summaries;
        $this->summaryString = "";
        foreach ($this->summaries as $v) {
            $this->summaryString .= $v . " ";
        }

        $this->title = $this->_json->title;

        return $this;
    }

    /**
     * get json object
     *
     * @access  public
     * @return  object  json object that the service returned
     */
    public function getJson ()
    {
        return $this->_json;
    }


    /**
     * execute service
     *
     * @param   string  $url    url you want to summarize
     * @return  string  summarized string
     * @access  public
     */
    public static function execute ($url = null)
    {
        if ($url === null) {
            throw new Exception ("require url execute : " . __METHOD__);
        }
        $obj = new Services_ThreeLines($url);
        return $obj->request()->parse()->summaryString;
    }

}

if (debug_backtrace()) {
    return;
}

echo <<<EOF
Usage
    
--
<?php
require_once 'Services/ThreeLines.php';
Services_ThreeLines::execute("http://example.com");

--
<?php
\$obj = new Services_ThreeLines("http://example.com/");
\$summery = \$obj->request()->parse()->summaryString;

EOF;


