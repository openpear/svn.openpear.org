<?php
ini_set("include_path", dirname(__FILE__)."/src/" . PATH_SEPARATOR . ini_get("include_path"));
require_once "Stream/Macro.php";

// Test
$opts = array();
$opts['debug'] = true;

Stream_Macro::registByArray('macro', $opts);

echo "\r\n**************************************\r\n";
echo "** Normal include";
echo "\r\n**************************************\r\n";
include "testdata.php";

echo "\r\n**************************************\r\n";
echo "** Include by macro stream";
echo "\r\n**************************************\r\n";
include "macro://testdata.php";


?>
