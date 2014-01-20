<?php

require_once('IO/Zlib.php');

$filename = $argv[1];

if ($filename === '-')  {
   $filename = 'php://stdin';
}

$zlibdata = file_get_contents($filename);

$zip = new IO_Zlib();
$zip->parse($zlibdata);
$zip->dump();
