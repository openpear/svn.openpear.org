<?php
ini_set("include_path", dirname(__FILE__)."/../" . PATH_SEPARATOR . ini_get("include_path"));
require_once "Services/Hanako.php";

$meter = new Services_Hanako('03', '50810100');
var_dump($meter->now());
