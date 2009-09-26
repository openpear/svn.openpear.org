<?php

/**
 * An interface for bit.ly
 *
 * @category    Services
 * @package     Services_Bitly
 * @author      tknzk <info@tknzk.com>
 * @copyright   Copyright (c) 2009, tknzk.com All rights reserved.
 * @license     BSD License
 * @link        http://openpear.org/package/Services_Bitly
 * @link        http://bit.ly
 *
 */

require_once 'Services/Bitly/Exception.php';

class Services_Bitly
{
    const DEBUG = false;

    const BITLY_API_URL = 'http://api.bit.ly';

    const JMP_API_URL = 'http://api.j.mp';

    const FORMAT = 'json';

    const VERSION = '0.1.0';

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

        if($response === false) {
            throw new Services_Bitly_Exception(curl_error($curl), curl_errno($curl));
        }

        curl_close($curl);

        if($this->format === 'json') {

            $json = json_decode($response,true);

            if($json['errorCode'] === 0 && $json['statusCode'] === 'OK') {

                if($json['results'][$longurl]['errorCode'] !== null) {

                    throw new Services_Bitly_Exception($json['results'][$longurl]['errorMessage'], $json['results'][$longurl]['errorCode']);

                }else{

                    return $json['results'][$longurl]['shortUrl'];

                }

            }else{

                throw new Services_Bitly_Exception($json['errorMessage'], $json['errorCode']);

            }
        }

        if($this->format === 'xml') {

            require_once 'XML/Unserializer.php';

            $unserializer = new XML_Unserializer(array(XML_UNSERIALIZER_OPTION_ATTRIBUTES_PARSE => 'parseAttributes'));
            $unserializer->unserialize($response);
            $xml = $unserializer->getUnserializedData();


            if($xml['errorCode'] == 0 && $xml['statusCode'] == 'OK') {

                if(is_array($xml['results'][$longurl])) {

                    throw new Services_Bitly_Exception($xml['results'][$longurl]['errorMessage'], (int)($xml['results'][$longurl]['errorCode']));

                }else{

                    return $xml['results']['nodeKeyVal']['shortUrl'];

                }

            }else{

                throw new Services_Bitly_Exception($xml['errorMessage'], (int)($xml['errorCode']));

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

        if(!preg_match("/$reg_str/",$shorturl)) {
            throw new Services_Bitly_Exception("URL domain you tried to expand was invalid.");
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

        if($response === false) {
            throw new Services_Bitly_Exception(curl_error($curl), curl_errno($curl));
        }

        curl_close($curl);

        if($this->format === 'json') {

            $json = json_decode($response,true);

            $userhash = preg_replace("/$reg_str/", "", $shorturl);

            if($json['errorCode'] === 0 && $json['statusCode'] === 'OK') {

                if(is_array($json['results'][$userhash]['longUrl'])) {

                    throw new Services_Bitly_Exception($json['results'][$userhash]['longUrl']['errorMessage'], $json['results'][$userhash]['longUrl']['errorCode']);

                }else{

                    return $json['results'][$userhash]['longUrl'];

                }

            }else{

                throw new Services_Bitly_Exception($json['errorMessage'], $json['errorCode']);

            }
        }

        if($this->format === 'xml') {

            require_once 'XML/Unserializer.php';

            $unserializer = new XML_Unserializer(array(XML_UNSERIALIZER_OPTION_ATTRIBUTES_PARSE => 'parseAttributes'));
            $unserializer->unserialize($response);
            $xml = $unserializer->getUnserializedData();

            $userhash = preg_replace("/$reg_str/", "", $shorturl);

            if($xml['errorCode'] == 0 && $xml['statusCode'] == 'OK') {

                if(is_array($xml['results'][$userhash]['longUrl'])) {

                    throw new Services_Bitly_Exception($xml['results'][$userhash]['longUrl']['errorMessage'], (int)($xml['results'][$userhash]['longUrl']['errorCode']));

                }else{

                    return $xml['results'][$userhash]['longUrl'];

                }

            }else{

                throw new Services_Bitly_Exception($xml['errorMessage'], (int)($xml['errorCode']));

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
