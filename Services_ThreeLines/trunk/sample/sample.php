<?php
ini_set("include_path", dirname(__FILE__)."../../src/" . PATH_SEPARATOR . ini_get("include_path"));

require_once "Services/ThreeLines.php";

// Test
//$obj = new Services_ThreeLines("http://d.hatena.ne.jp/sotarok/");

try {
    //Services_ThreeLines::execute();
    $summary = Services_ThreeLines::execute("http://d.hatena.ne.jp/sotarok/");
    echo $summary;
}
catch (Exception $e) {
    echo $e->getMessage();
}

?>
