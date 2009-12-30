<?php
require_once('Services/Rakuten.php');
define('DEV_ID', 'xxxx');
define('AFF_ID', 'xxxx');

// 楽天書籍検索
$api = Services_Rakuten::factory('BookSearch', DEV_ID, AFF_ID);
$api->execute(array('keyword' => 'ブログ'));
var_dump($api->getLastUrl());
var_dump($api->getResultData());

// 楽天CD検索
$api = Services_Rakuten::factory('CDSearch', DEV_ID, AFF_ID);
$api->execute(array('keyword' => '氷室'));
var_dump($api->getLastUrl());
var_dump($api->getResultData());

// 楽天DVD検索
$api = Services_Rakuten::factory('DVDSearch', DEV_ID, AFF_ID);
$api->execute(array('keyword' => '氷室'));
var_dump($api->getLastUrl());
var_dump($api->getResultData());
?>
