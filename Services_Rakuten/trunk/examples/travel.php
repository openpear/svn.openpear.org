<?php
require_once('Services/Rakuten.php');
define('DEV_ID', 'xxxx');
define('AFF_ID', 'xxxx');

// 楽天トラベル施設検索
$api = Services_Rakuten::factory('SimpleHotelSearch', DEV_ID, AFF_ID);
$api->execute(array('largeClassCode' => 'japan', 'middleClassCode' => 'kanagawa', 'smallClassCode' => 'hakone'));
var_dump($api->getLastUrl());
var_dump($api->getResultData());

// 楽天トラベル施設情報
$api = Services_Rakuten::factory('HotelDetailSearch', DEV_ID, AFF_ID);
$api->execute(array('hotelNo' => '65638'));
var_dump($api->getLastUrl());
var_dump($api->getResultData());

// 楽天トラベル空室検索
$api = Services_Rakuten::factory('VacantHotelSearch', DEV_ID, AFF_ID);
$api->execute(array('largeClassCode' => 'japan', 'middleClassCode' => 'kanagawa', 'smallClassCode' => 'hakone'
                  , 'checkinDate' => '2007-07-18', 'checkoutDate' => '2007-07-20'));
var_dump($api->getLastUrl());
var_dump($api->getResultData());

// 楽天トラベル地区コード
$api = Services_Rakuten::factory('GetAreaClass', DEV_ID, AFF_ID);
$api->execute();
var_dump($api->getLastUrl());
var_dump($api->getResultData());

// 楽天トラベルキーワード検索
$api = Services_Rakuten::factory('KeywordHotelSearch', DEV_ID, AFF_ID);
$api->execute(array('keyword' => '伊豆'));
var_dump($api->getLastUrl());
var_dump($api->getResultData());
?>
