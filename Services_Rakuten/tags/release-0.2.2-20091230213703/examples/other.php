<?php
require_once('Services/Rakuten.php');
define('DEV_ID', 'xxxx');
define('AFF_ID', 'xxxx');

// 楽天ダイナミックアド
$api = Services_Rakuten::factory('DynamicAd', DEV_ID, AFF_ID);
$api->execute(array('url' => 'http://www.1x1.jp/blog/'));
var_dump($api->getResultStatus());
var_dump($api->getResultStatusMessage());
var_dump($api->getLastUrl());
var_dump($api->getResultData());
?>
