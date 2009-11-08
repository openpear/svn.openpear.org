<?php

/** 
 * An interface for vi.ly
 *
 * @category    Services
 * @package     Services_Vily
 * @author      tknzk <info@tknzk.com>
 * @copyright   Copyright (c) 2009, tknzk.com All rights reserved.
 * @license     BSD License
 * @link        http://openpear.org/package/Services_Vily
 * @link        http://vi.ly
 *
 */

require_once 'Services/Vily/Exception.php';

class Services_Vily
{
    const DEBUG = false;

    const VILY_API_URL = 'http://vi.ly';

    const VERSION = '0.0.1';

    /**
     * Default constructor
     *
     * @return  void
     *
     */
    public function __construct()
    {
    }

    /**
     * Create Short URL
     *
     * @access      public
     * @param       string $longurl
     * @return      string
     * @static
     *
     */
    public function shorten($longurl)
    {
        $baseurl = self::VILY_API_URL;

        $apiurl = $baseurl  . '/api?'
                            . 'url='    . $longurl
                            . '';

        $curl   = curl_init();
        curl_setopt($curl,  CURLOPT_URL,            $apiurl);
        curl_setopt($curl,  CURLOPT_HEADER,         false);
        curl_setopt($curl,  CURLOPT_RETURNTRANSFER, true);

        $response   = curl_exec($curl);

        if($response === false) {
            throw new Services_Vily_Exception(curl_error($curl), curl_errno($curl));
        }

        curl_close($curl);

        return $response;
    }
}
