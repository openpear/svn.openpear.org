<?php

/**
 * A interface for calil
 *
 * @category    Services
 * @package     Services_Calil
 * @author      tknzk <info@tknzk.com>
 * @copyright   Copryright (c) 2010, tknzk.com All rigths reserved.
 * @license     BSD License
 * @link        http://openpear.org/package/Services_Calil
 * @link        http://calil.jp
 *
 */

require_once 'Services/Calil/Exception.php';

class Services_Calil
{
    const API_URL       = 'http://api.calil.jp';
    const FORMAT_JSON   = 'json';
    const FORMAT_XML    = 'xml';

    const VERSION       = '0.0.1';

    private $apiKey;
    private $pref;
    private $systemid;
    private $geocode;
    private $isbn;
    private $session;
    private $format;
    private $callback;

    /**
     * Default constructor
     *
     * @return  viod
     * @param   string $apiky
     */
    public function __construct($apikey)
    {
        if (empty($apikey)) {
            throw new Services_Calil_Exception('apikey is required.');
        }

        $this->setApikey($apikey);
    }

    /**
     * library
     * 
     * @param  array $paramas
     */
    public function library($params = array())
    {
        self::setParameters($params);

        if (empty($this->pref) && empty($this->systemid) && empty($this->geocode)) {
            throw new Services_Calil_Exception('pref or systemid or geocode are required.');
        }

        $apiurl = self::API_URL . '/library'
                                . '?apikey='    . $this->apikey
                                . '&pref='      . $this->pref
                                . '&systemid='  . $this->systemid
                                . '&geocode='   . $this->geocode
                                . '&format='    . $this->format
                                . '&callback='  . $this->callback
                                . '';

        return self::exeCurl($apiurl);
    }

    /**
     * check
     * 
     * @param  array $paramas
     */
    public function check($params = array())
    {
        self::setParameters($params);

        if (empty($this->isbn) && empty($this->systemid)) {
            throw new Services_Calil_Exception('isbn and systemid are required.');
        }

        $apiurl = self::API_URL . '/check'
                                . '?apikey='    . $this->apikey
                                . '&isbn='      . $this->isbn
                                . '&systemid='  . $this->systemid
                                . '&format='    . $this->format
                                . '&callback='  . $this->callback
                                . '';

        return self::exeCurl($apiurl);
    }

    /**
     * polling
     * 
     * @param  array $paramas
     */
    public function polling($params = array())
    {
        self::setParameters($params);

        if (empty($this->session)) {
            throw new Services_Calil_Exception('session is required.');
        }

        $apiurl = self::API_URL . '/check'
                                . '?apikey='    . $this->apikey
                                . '&session='   . $this->session
                                . '&format='    . $this->format
                                . '&callback='  . $this->callback
                                . '';

        return self::exeCurl($apiurl);
    }


    /**
     * execute curl
     *
     * @param   string  $url
     * @reutrn  array   $response
     */
    private function exeCurl($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL,             $url);
        curl_setopt($curl, CURLOPT_HEARDER,         false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER,  true);

        $response = curl_exec($curl);

        if ($response == false) {
            throw new Services_Calil_Exception(curl_error($curl), curl_errno($curl));
        }

        curl_close($curl);

        return $response;
    }

    /**
     * Set parameters
     * 
     * @param   array  $params
     */
    private function setParameters($params = array())
    {
        self::resetParameters();

        if (isset($params['pref']) && $params['pref'] != null) {
            $this->setPref($params['pref']);
        }

        if (isset($params['systemid']) && $params['systemid'] != null) {
            $this->setSystemid($params['systemid']);
        }

        if (isset($params['geocode']) && $params['geocode'] != null) {
            $this->setGeocode($params['geocode']);
        }

        if (isset($params['isbn']) && $params['isbn'] != null) {
            $this->setIsbn($params['isbn']);
        }

        if (isset($params['session']) && $params['session'] != null) {
            $this->setSession($params['session']);
        }

        if (isset($params['format']) && $params['format'] != null) {
            $this->setFormat($params['format']);
        }

        if (isset($params['callback']) && $params['callback'] != null) {
            $this->setCallback($params['callback']);
        }
    }
    
    /**
     * Reset parameters
     * 
     */
    private function resetParameters()
    {
        $this->setPref(null);
        $this->setSystemid(null);
        $this->setGeocode(null);
        $this->setIsbn(null);
        $this->setSession(null);
        $this->setFormat(null);
        $this->setCallback(null);
    }

    /**
     * Set apikey
     * 
     * @param   string  $apikey
     */
    private function setApikey($apikey)
    {
        $this->apiKey = (string) $apikey;
    }

    /**
     * Set pref
     * 
     * @param   string  $pref
     */
    private function setPref($pref)
    {
        $this->pref = (string) $pref;
    }

    /**
     * Set systemid
     * 
     * @param   string  $systemid
     */
    private function setSystemid($systemid)
    {
        $this->systemid = (string) $systemid;
    }

    /**
     * Set geocode
     * 
     * @param   string  $geocode
     */
    private function setGeocode($geocode)
    {
        $this->geocode = (string) $geocode;
    }

    /**
     * Set isbn
     * 
     * @param   string  $isbn
     */
    private function setIsbn($isbn)
    {
        $this->isbn = (string) $isbn;
    }

    /**
     * Set session
     * 
     * @param   string  $session
     */
    private function setSession($session)
    {
        $this->session = (string) $session;
    }

    /**
     * Set format
     * 
     * @param   string  $format
     */
    private function setFormat($format)
    {
        $this->format = (string) $format;
    }

    /**
     * Set callback
     * 
     * @param   string  $callback
     */
    private function setCallback($callback)
    {
        $this->callback = (string) $callback;
    }

}
