<?php

require_once('IO/Zlib.php');

$zlibdata = file_get_contents($argv[1]);

$zip = new IO_Zlib();
echo $zip->inflate($zlibdata);
