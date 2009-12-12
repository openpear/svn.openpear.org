<?php
set_include_path(dirname(__FILE__) . '/../library' . PATH_SEPARATOR . get_include_path());

require_once 'Diggin/Http/Response/CharactorEncoding.php';
require_once 'Zend/Http/Client.php';

$url = $argv[1];
$client = new Zend_Http_Client($url);
$response = $client->request();

var_dump(Diggin_Http_Response_CharactorEncoding::detect($response, $response->getHeader('content-type')));
$wrapper = Diggin_Http_Response_CharactorEncoding::createWrapper($response);
var_dump($wrapper instanceof Zend_Http_Response); //true
var_dump($wrapper->getBody()); //UTF-8
