<?php

require_once 'IO/SMAF.php';

$smafdata = file_get_contents($argv[1]);
$smaf = new IO_SMAF();
$smaf->parse($smafdata);
$smaf->dump();


