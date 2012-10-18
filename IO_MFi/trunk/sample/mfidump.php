<?php

require_once 'IO/MFi.php';

$mfidata = file_get_contents($argv[1]);
$mfi = new IO_MFI();
$mfi->parse($mfidata);
$mfi->dump();

