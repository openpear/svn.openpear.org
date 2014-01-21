<?php

require_once('IO/Zlib.php');

$data = file_get_contents($argv[1]);

$zip = new IO_Zlib();
echo $zip->deflate($data);
