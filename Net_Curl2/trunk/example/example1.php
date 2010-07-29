<?php
require_once('Net/Curl2.php');

$curl = new Net_Curl2();
$curl->setURL('http://www.google.co.jp/');
$curl->request();

printf("Status: %d\n", $curl->getStatus());
printf("Info: %s\n", print_r($curl->getInfo(), true));
printf("Header: %s\n", print_r($curl->getHeader(), true));
printf("Body; %s\n", $curl->getBody());
