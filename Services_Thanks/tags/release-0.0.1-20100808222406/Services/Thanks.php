<?php

/**
 * An interface for Thanks
 *
 * @category    Services
 * @package     Services_Thanks
 * @author      tknzk <info@tknzk.com>
 * @copyright   Copyright (c) 2010, tknzk.com All rights reserved.
 * @license     BSD License
 * @link        http://openpear.org/package/Services_Thanks
 * @link        http://thanks.kayac.com
 *
 */

require_once 'PEAR/Exception.php';

class Services_Thanks
{
    const API_BASE_URL          = 'http://thanks.kayac.com';

    const POST_HEADER_JSON_UTF8 = 'Content-Type: application/json; charset=utf8';

    const VERSION               = '0.0.1';

    /**
     * API KEY
     */
    private $apyKey;

    /**
     * PostParameters
     */
    private $postParameters;

    /**
     * API URL
     */
    private $apiUrl;

    /**
     * Default Constructor
     */
    public function __construct($apiKey)
    {
        $this->setApiKey($apiKey);
    }

    /**
     * Say Thanks message
     */
    public function say($toName, $body, $publicYn, $tag = array())
    {
        $this->setApiUrl = self::API_BASE_URL . '/api/pub/say/thanks';

        $postParameters = array();
        $postParameters['api_key']      = $this->apyKey;
        $postParameters['to_name']      = $toName;
        $postParameters['body']         = $body;
        $postParameters['public_yn']    = $publicYn;
        $postParameters['tag']          = explode(" ", $tag);

        $this->postParameters = $postParameters;

        $response = self::call();

        $json = json_decode($response, true);

        if ($json['result'] == 'success' && $json['status'] == 201) {

            return true;

        } else if ($json['action'] == 'pub:sayThanks' && $json['status'] == 403) {

            throw new PEAR_Exception('say thanks error.', $json['status']);

        }
    }

    /**
     * Say Thanks message from guest
     */
    public function guestSay($toName, $guestName, $body, $publicYn, $tag = array())
    {
        $this->setApiUrl = self::API_BASE_URL . '/api/pub/say/guest_thanks';

        $postParameters = array();
        $postParameters['api_key']      = $this->apyKey;
        $postParameters['to_name']      = $toName;
        $postParameters['guest_name']   = $guestName;
        $postParameters['body']         = $body;
        $postParameters['public_yn']    = $publicYn;
        $postParameters['tag']          = explode(" ", $tag);

        $this->postParameters = $postParameters;

        $response = self::call();
        
        $json = json_decode($response, true);

        if ($json['result'] == 'success' && $json['status'] == 201) {

            return true;

        } elseif ($json['action'] == 'pub:sayGuestThanks' && $json['status'] == 403) {

            throw new PEAR_Exception('guest say thanks error.', $json['status']);

        }
    }

    /**
     * Read Thanks message
     */
    public function read($page)
    {
        $this->setApiUrl = self::API_BASE_URL . '/api/pub/read/thanks';

        $postParameters = array();
        $postParameters['api_key']  = $this->apyKey;
        $postParameters['page']     = $page;

        $this->postParameters = $postParameters;

        $response = self::call();

        $json = json_decode($response, true);

        if ($json['result'] == 'success') {

            return $json;

        } elseif ($json['action'] == 'pub:readThanks' && $json['status'] == 403) {

            throw new PEAR_Exception('read thanks error.', $json['status']);

        }
    }

    /**
     * Read Thanks message from geuest
     */
    public function guestRead($page)
    {
        $this->setApiUrl = self::API_BASE_URL . '/api/pub/read/guest_thanks';

        $postParameters = array();
        $postParameters['api_key']  = $this->apyKey;
        $postParameters['page']     = $page;

        $this->postParameters = $postParameters;

        $response = self::call();

        $json = json_decode($response, true);

        if ($json['result'] == 'success') {

            return $json;

        } elseif ($json['action'] == 'pub:readGuestThanks' && $json['status'] == 403) {

            throw new PEAR_Exception('read thanks error.', $json['status']);

        }
    }

    /**
     * Delete Thanks message
     */
    public function delete($id)
    {
        $this->setApiUrl = self::API_BASE_URL . '/api/pub/delete/thanks';

        $postParameters = array();
        $postParameters['api_key']  = $this->apyKey;
        $postParameters['id']       = $id;

        $this->postParameters = $postParameters;

        $response = self::call();

        $json = json_decode($response, true);

        if ($json['result'] == 'success' && $json['status'] == 200) {

            return true;

        } elseif ($json['action'] == 'pub:deleteThanks' && $json['status'] == 403) {

            throw new PEAR_Exception('delete thanks error.', $json['status']);

        }
    }

    /**
     * call
     */
    private function call()
    {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL,         $this->apiUrl);
        curl_setopt($curl, CURLOPT_HTTPDEADER,  self::POST_HEADER_JSON_UTF8);
        curl_setopt($curl, CURLOPT_POST,        1);
        curl_setopt($curl, CURLOPT_POSTFIELDS,  $this->postParameters);

        $response = curl_exec($curl);
        
        if ($response == false) {
            throw new PEAR_Exception(curl_error($curl), curl_errno($curl);
        }

        curl_close($curl);

        return $response;
    }

    /**
     * Set API URL
     */
    private function setApiUrl($apiUrl)
    {
        $this->apiUrl = $apiUrl;
    }

    /**
     * Set PostParameters
     */
    private function setPostParameters($postParameter = array())
    {
        $this->postParameters = $postParameter
    }

    /**
     * Set API KEY
     */
    private function setApiKey($apiKey)
    {
        $this->apiKey = (string) $apiKey;
    }

}
