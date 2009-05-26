<?php

/**
 * Webservices for Hanako-san
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
 * @package   Services_Hanako
 * @author    Hideyuki Shimooka <shimooka@doyouphp.jp>
 * @copyright 2007 Hideyuki Shimooka
 * @license   http://www.php.net/license/3_01.txt The PHP License, version 3.01
 * @version   0.1.0
 * @link      http://d.hatena.ne.jp/shimooka/
 * @see       http://search.cpan.org/~hamano/WWW-Hanako-0.05/
 */

require_once 'HTTP/Request2.php';

/**
 * the version number of this package
 */
define('SERVICES_HANAKO_VERSION',    '0.1.0');

/**
 * User-Agent for the request
 */
define('SERVICES_HANAKO_USER_AGENT', 'Services_Hanako/'.SERVICES_HANAKO_VERSION);

/**
 * the request URL
 */
define('SERVICES_HANAKO_COOKIE_URL', 'http://kafun.taiki.go.jp/Hyou0.aspx');
define('SERVICES_HANAKO_INFO_URL',   'http://kafun.taiki.go.jp/Hyou2.aspx');

/**
 * Webservices for Hanako
 *
 * @category  Services
 * @package   Services_Hanako
 * @author    Hideyuki Shimooka <shimooka@doyouphp.jp>
 * @copyright 2007 Hideyuki Shimooka
 * @license   http://www.php.net/license/3_01.txt The PHP License, version 3.01
 * @version   0.1.0
 * @link      http://d.hatena.ne.jp/shimooka/
 */
class Services_Hanako {

    /**
     * parsed data
     * @var    array
     * @access private
     */
    private $description;

    /**
     * the User-Agent you use
     * @var    string
     * @access private
     */
    private $user_agent;

    /**
     * the given area code
     * @var    unknown
     * @access private
     */
    private $area_code;

    /**
     * the given master code
     * @var    unknown
     * @access private
     */
    private $master_code;

    /**
     * HTTP_Request2 object
     * @var    object
     * @access private
     */
    private $request;

    /**
     * Constructor
     *
     * @param  object HTTP_Request2 $request HTTP_Request2 object
     * @param  string    $area_code the area code
     * @param  string    $master_code the master code
     * @access public
     * @throws Exception
     */
    public function __construct(HTTP_Request2 $request, $area_code, $master_code) {
        if (!preg_match('#^\\d{2}$#', $area_code)) {
            throw new Exception('Invalid area code : [' . $area_code . ']');
        }
        if (!preg_match('#^\\d{8}$#', $master_code)) {
            throw new Exception('Invalid master code : [' . $master_code . ']');
        }
        $this->request = $request;
        $this->area_code = $area_code;
        $this->master_code = $master_code;
        $this->user_agent = SERVICES_HANAKO_USER_AGENT;
        $this->description = null;
    }

    /**
     * return the version number of this package
     *
     * @return string the version number
     * @access public
     */
    public function getVersion() {
        return SERVICES_HANAKO_VERSION;
    }

    /**
     * return the User-Agent you set
     *
     * @return string the User-Agent
     * @access public
     */
    public function getUserAgent() {
        return $this->user_agent;
    }

    /**
     * set the User-Agent you want
     *
     * @param  string $user_agent the User-Agent
     * @return void
     * @access public
     */
    public function setUserAgent($user_agent = null) {
        if (is_null($user_agent) || $user_agent === '') {
            $this->user_agent = SERVICES_HANAKO_USER_AGENT;
        } else {
            $this->user_agent = $user_agent;
        }
    }

    /**
     * return the area code
     *
     * @return string the area code
     * @access public
     */
    public function getAreaCode() {
        return $this->area_code;
    }

    /**
     * return the master code
     *
     * @return string the master code
     * @access public
     */
    public function getMasterCode() {
        return $this->master_code;
    }

    /**
     * return the request URL to get cookie
     *
     * @return string url
     * @access public
     */
    public function getRequestUrlToGetCookie() {
        return SERVICES_HANAKO_COOKIE_URL
             . sprintf('?MstCode=%s&AreaCode=%s',
                       $this->master_code,
                       $this->area_code);
    }

    /**
     * return the request URL to get information
     *
     * @return string url
     * @access public
     */
    public function getRequestUrlToGetInformation() {
        return SERVICES_HANAKO_INFO_URL;
    }

    /**
     * execute a request, fetch and parse
     *
     * @return boolean true
     * @access private
     * @throws Exception throws Exception if any errors occur
     */
    private function fetchDescription() {
        // get cookie
        $this->request->setUrl($this->getRequestUrlToGetCookie());
        $this->request->setHeader('User-Agent', SERVICES_HANAKO_USER_AGENT);
        $response = $this->request->send();
        switch ($response->getStatus()) {
        case 200:
            break;
        default:
            throw new Exception('Hanako: return HTTP ' . $response->getStatus());
        }

        // get information
        $cookies = $response->getCookies();
        if (!isset($cookies[0])) {
            throw new Exception('Hanako: failed to get cookie');
        }
        $this->request->addCookie($cookies[0]['name'], $cookies[0]['value']);
        $this->request->setUrl($this->getRequestUrlToGetInformation());
        $response = $this->request->send();
        switch ($response->getStatus()) {
        case 200:
            break;
        default:
            throw new Exception('Hanako: return HTTP ' . $response->getStatus());
        }

        $this->parseDescription($response->getBody());

        return true;
    }

    /**
     * parse a response body
     *
     * @param  string $body a response body
     * @return boolean true if success. false if failed
     * @access private
     */
    private function parseDescription($body) {
        $this->description = array();
        $pattern = '#<td><font size="2">([^<]*)</font></td><td align="Right"><font size="2">([^<]*)</font></td><td align="Right"><font size="2">([^<]*)</font></td><td align="Right"><font size="2">([^<]*)</font></td><td align="Right"><font size="2">([^<]*)</font></td><td align="Right"><font size="2">([^<]*)</font></td><td align="Right"><font size="2">([^<]*)</font></td>#';

        if (preg_match_all($pattern, $body, $matches)) {
            $this->description['hour'] = array_pop($matches[1]);
            $this->description['pollen'] = array_pop($matches[2]);
            $this->description['wd'] = array_pop($matches[3]);
            $this->description['ws'] = array_pop($matches[4]);
            $this->description['temp'] = array_pop($matches[5]);
            $this->description['prec'] = array_pop($matches[6]);
            $this->description['prec_bool'] = array_pop($matches[7]);
        }
    }

    /**
     * return a current information
     *
     * @return mixed  a current information
     * @access public
     */
    public function now() {
        if (is_null($this->description)) {
            $ret = $this->fetchDescription();
        }
        return $this->description;
    }

}
