<?php

/**
 * An interface for bit.ly
 *
 * @category    Services
 * @package     Services_Bitly
 * @author      tknzk <info@tknzk.com>
 * @copyright   Copyright (c) 2009-2010, tknzk.com All rights reserved.
 * @license     BSD License
 * @link        http://openpear.org/package/Services_Bitly
 * @link        http://bit.ly
 *
 */

require_once 'Services/Bitly/Exception.php';

class Services_Bitly
{
    const API_URL_BITLY = 'http://api.bit.ly';
    const API_URL_JMP   = 'http://api.j.mp';

    const DOMAIN_BITLY  = 'bit.ly';
    const DOMAIN_JMP    = 'j.mp';

    const FORMAT_JSON   = 'json';
    const FORMAT_XML    = 'xml';

    const API_VERSION   = '2.0.1';

    const VERSION       = '0.2.0';


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
    private $apiKey;

    /**
     * API Version
     *
     * @var string
     */
    private $apiVersion;

    /**
     * Base Domain
     *
     * @var string
     */
    private $baseDamain;

    /**
     * Base Url
     *
     * @var string
     */
    private $baseUrl;

    /**
     * regex string
     *
     * @var string
     */
    private $regexString;

    /**
     * Default constructor
     *
     * @return  void
     * @param   string  @login
     * @param   string  @apikey
     * @param   string  @apiversion
     * @param   string  @format
     */
    public function __construct($login, $apikey, $domain = self::DOMAIN_BITLY, $format = self::FORMAT_JSON)
    {
        $this->setLogin($login);
        $this->setApikey($apikey);
        $this->setFormat($format);
        $this->setBaseDomain($domain);
        $this->setApiVersion(self::API_VERSION);
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

        $apiurl = $this->baseUrl    . '/shorten?'
                                    . 'version='    . $this->apiVersion
                                    . '&longUrl='   . urlencode($longurl)
                                    . '&login='     . $this->login
                                    . '&apiKey='    . $this->apiKey
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

        if($this->format === self::FORMAT_JSON) {

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

        if($this->format === self::FORMAT_XML) {

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

        if (!preg_match("/^$this->regexString/", $shorturl)) {
            throw new Services_Bitly_Exception("URL domain you tried to expand was invalid.");
        }

        $apiurl = $this->baseUrl    . '/expand?'
                                    . 'version='    . $this->apiVersion
                                    . '&shortUrl='  . urlencode($shorturl)
                                    . '&login='     . $this->login
                                    . '&apiKey='    . $this->apiKey
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

        if($this->format === self::FORMAT_JSON) {

            $json = json_decode($response,true);

            $userhash = preg_replace("/^$this->regexString/", "", $shorturl);

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

        if($this->format === self::FORMAT_XML) {

            require_once 'XML/Unserializer.php';

            $unserializer = new XML_Unserializer(array(XML_UNSERIALIZER_OPTION_ATTRIBUTES_PARSE => 'parseAttributes'));
            $unserializer->unserialize($response);
            $xml = $unserializer->getUnserializedData();

            $userhash = preg_replace("/^$this->regexString/", "", $shorturl);

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
    private function setLogin($login)
    {
        $this->login = (string) $login;
    }

    /**
     * Set apikey
     *
     * @return  void
     * @param   string  $apikey
     */
    private function setApikey($apikey)
    {
        $this->apiKey = (string) $apikey;
    }

    /**
     * Set apiversion
     *
     * @return  void
     * @param   string  $apiversion
     */
    private function setApiVersion($apiversion)
    {
        $this->apiVersion = (string) $apiversion;
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
     * Set Base Domain
     *
     * @return  bool
     * @parma   string  $domain
     */
    public function setBaseDomain($domain = self::DOMAIN_BITLY)
    {
        $this->baseDomain   = (string) $domain;
        switch($domain) {
            case self::DOMAIN_BITLY:
                $this->baseUrl = self::API_URL_BITLY;
                break;
            case self::DOMAIN_JMP:
                $this->baseUrl = self::API_URL_JMP;
                break;
            default:
                $this->baseUrl = self::API_URL_BITLY;
        }
        $this->regexString  = (string) 'http:\/\/' . $domain . '\/';
    }

}
