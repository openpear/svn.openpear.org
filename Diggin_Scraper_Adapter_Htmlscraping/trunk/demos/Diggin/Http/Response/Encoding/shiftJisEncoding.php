<?php
require_once 'Diggin/Http/Response/Encoding.php';
require_once 'HTTP/Request2.php';

$req = new HTTP_Request2();
$req->setUrl('http://ugnews.net/');
$response = $req->send();
$encoded = Diggin_Http_Response_Encoding::encodeResponseObject($response);

var_dump(strip_tags($encoded)); //utf-8