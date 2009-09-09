<?php
/**
 *
 *
 */

require_once 'Services/Prowl.php';
require_once 'config.php';


$prowl = new Services_Prowl($api_key);
$result = $prowl->push('aaaaa');

var_dump($result);
