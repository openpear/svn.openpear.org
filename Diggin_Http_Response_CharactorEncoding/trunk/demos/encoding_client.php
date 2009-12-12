<?php

$url = isset($argv[1]) ? $argv[1] : 'http://ugnews.net/';

set_include_path(dirname(__FILE__) . '/../library' . PATH_SEPARATOR . get_include_path());
require_once 'Diggin/Http/Response/CharactorEncoding.php';
require_once 'Zend/Http/Client.php';

class Example_Http_Client extends Zend_Http_Client
{
    public function request($method = null)
    {
        $response = parent::request($method);

        return Diggin_Http_Response_CharactorEncoding::createWrapper($response);
    }
}

$client = new Example_Http_Client($url);

print_r($client->request()->getBody());
