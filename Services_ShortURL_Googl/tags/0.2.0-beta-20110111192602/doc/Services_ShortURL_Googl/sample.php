<?php
ini_set("include_path", dirname(__FILE__) . "/../../" . PATH_SEPARATOR . ini_get("include_path"));
require_once "Services/ShortURL/Googl.php";

// Test
//$obj = Services_ShortURL::factory('Googl');
$obj = new Services_ShortURL_Googl();
try {
    $result = $obj->shorten('http://d.hatena.ne.jp/shimooka/');
    echo $result . PHP_EOL;
    echo $obj->expand($result) . PHP_EOL;
} catch (Exception $e) {
    echo $e->getMessage() . PHP_EOL;
    exit;
}
