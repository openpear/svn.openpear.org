<?php
require_once('Services/Rakuten.php');
define('DEV_ID', 'xxxx');
define('AFF_ID', 'xxxx');

// 楽天商品検索
$api = Services_Rakuten::factory('ItemSearch', DEV_ID, AFF_ID);
$api->execute(array('keyword' => '大福'));
var_dump($api->getLastUrl());
var_dump($api->getResultData());

// 楽天商品ジャンル検索
$api = Services_Rakuten::factory('GenreSearch', DEV_ID, AFF_ID);
$api->execute();
var_dump($api->getLastUrl());
var_dump($api->getResultData());

// 楽天商品コード検索
$api = Services_Rakuten::factory('ItemCodeSearch', DEV_ID, AFF_ID);
$api->execute(array('itemCode' => 'book:11907840'));
var_dump($api->getLastUrl());
var_dump($api->getResultData());

// 楽天カタログ検索
$api = Services_Rakuten::factory('CatalogSearch', DEV_ID, AFF_ID);
$api->execute(array('keyword' => 'ワンセグ'));
var_dump($api->getLastUrl());
var_dump($api->getResultData());
?>
