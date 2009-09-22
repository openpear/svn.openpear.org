<?php

/**
 * An interface for bit.ly
 *
 * @category    Services
 * @package     Services_Bitly
 * @author      tknzk <info@tknzk.com>
 * @copyright   Copyright (c) 2009, tknzk.com All rights reserved.
 * @license     BSD License
 * @link        http://bit.ly
 *
 */

class Services_Bitly
{
    const DEBUG = false;

    const BITLY_API_URL = 'http://api.bit.ly';

    const JMP_API_URL = 'http://api.j.mp';

    const FORMAT = 'json';

    const VERSION = '0.0.1';

    const API_VERSION = '2.0.1';

    /**
     * API Login Account
     *
     * @var string
     */
    private $login;

    /**
     * API Key
     *
     * @var string
     */
    private $apikey;

    /**
     * API Version
     *
     * @var string
     */
    private $apiversion;

    /**
     * Default constructor
     *
     * @return  void
     * @param   string  @login
     * @param   string  @apikey
     * @param   string  @apiversion
     * @param   string  @format
     */
    public function __construct($login, $apikey, $apiversion = self::API_VERSION, $format = self::FORMAT)
    {
        if($login !== null) {
            $this->setLogin($login);
        }

        if($apikey !== null) {
            $this->setApikey($apikey);
        }

        if($apiversion !== null) {
            $this->setApiVersion($apiversion);
        }

        if($format !== null) {
            $this->setFormat($format);
        }

        $this->changedomain = false;
    }

    /**
     * Create Short URL
     *
     * @access      public
     * @param       string  $longurl
     * @return      string
     * @static
     */
    public function shorten($longurl)
    {
        if($this->changedomain === false) {
            $baseurl = self::BITLY_API_URL;
        }else{
            $baseurl = self::JMP_API_URL;
        }

        $apiurl = $baseurl  . '/shorten?'
                            . 'version='    . $this->apiversion
                            . '&longUrl='   . urlencode($longurl)
                            . '&login='     . $this->login
                            . '&apiKey='    . $this->apikey
                            . '&format='    . $this->format
                            . '';

        $curl   = curl_init();
        curl_setopt($curl,  CURLOPT_URL,            $apiurl);
        curl_setopt($curl,  CURLOPT_HEADER,         false);
        curl_setopt($curl,  CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        curl_close($curl);

        if($this->format === 'json') {

            $json = json_decode($response,true);

            if($json['errorCode'] === 0 && $json['statusCode'] === 'OK') {
                return $json['results'][$longurl]['shortUrl'];
            }else{
                return false;
            }
        }

        if($this->format === 'xml') {
            $xml = simplexml_load_string($response);

            if($xml->errorCode == 0 && $xml->statusCode == 'OK') {
                return $xml->results->nodeKeyVal->shortUrl;
            }else{
                return false;
            }
        }
    }

    /**
     * Expand Long URL
     *
     * @access      public
     * @param       string  $shorurl
     * @return      string
     * @static
     */
    public function expand($shorturl)
    {
        if($this->changedomain === false) {

            $reg_str = 'http:\/\/bit.ly\/';

            if(preg_match("/$reg_str/",$shorturl)) {
                $baseurl = self::BITLY_API_URL;
            }else{
                $baseurl = self::JMP_API_URL;
                $reg_str = 'http:\/\/j.mp\/';
            }

        }else{

            $reg_str = 'http:\/\/j.mp\/';

            if(preg_match("/$reg_str/",$shorturl)) {
                $baseurl = self::JMP_API_URL;
            }else{
                $baseurl = self::BITLY_API_URL;
                $reg_str = 'http:\/\/bit.ly\/';
            }

        }


        $apiurl = $baseurl  . '/expand?'
                            . 'version='    . $this->apiversion
                            . '&shortUrl='  . urlencode($shorturl)
                            . '&login='     . $this->login
                            . '&apiKey='    . $this->apikey
                            . '&format='    . $this->format
                            . '';

        $curl   = curl_init();
        curl_setopt($curl,  CURLOPT_URL,            $apiurl);
        curl_setopt($curl,  CURLOPT_HEADER,         false);
        curl_setopt($curl,  CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        curl_close($curl);

        if($this->format === 'json') {

            $json = json_decode($response,true);

            $userhash = preg_replace("/$reg_str/", "", $shorturl);

            if($json['errorCode'] === 0 && $json['statusCode'] === 'OK') {
                return $json['results'][$userhash]['longUrl'];
            }else{
                return false;
            }
        }

        if($this->format === 'xml') {

            $xml = simplexml_load_string($response);

            $userhash = preg_replace("/$reg_str/", "", $shorturl);

            if($xml->errorCode == 0 && $xml->statusCode == 'OK') {
                return $xml->results->$userhash->longUrl;
            }else{
                return false;
            }
        }
    }

    /**
     * Set login
     *
     * @return  void
     * @param   string  $login
     */
    public function setLogin($login)
    {
        $this->login = (string) $login;
    }

    /**
     * Set apikey
     *
     * @return  void
     * @param   string  $apikey
     */
    public function setApikey($apikey)
    {
        $this->apikey = (string) $apikey;
    }

    /**
     * Set apiversion
     *
     * @return  void
     * @param   string  $apiversion
     */
    public function setApiVersion($apiversion)
    {
        $this->apiversion = (string) $apiversion;
    }

    /**
     * Set format
     *
     * @return  void
     * @param   string  $format
     */
    public function setFormat($format)
    {
        $this->format = (string) $format;
    }

    /**
     * Change Base Domain
     *
     * @return  void
     * @parma   bool    $change
     */
    public function changeBaseDomain($change = true)
    {
        $this->changedomain = $change;
    }

}
