<?php
require_once('Services/Rakuten.php');
define('DEV_ID', 'xxxx');
define('AFF_ID', 'xxxx');

// 楽天オークション商品検索
$api = Services_Rakuten::factory('AuctionItemSearch', DEV_ID, AFF_ID);
$api->execute(array('keyword' => 'ブログ'));
var_dump($api->getLastUrl());
var_dump($api->getResultData());

// 楽天オークション商品コード検索
$api = Services_Rakuten::factory('AuctionItemCodeSearch', DEV_ID, AFF_ID);
$api->execute(array('itemCode' => 'i:aaa:1234567'));
var_dump($api->getLastUrl());
var_dump($api->getResultData());
?>
