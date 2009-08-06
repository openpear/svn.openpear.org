<?php
include_once dirname(__FILE__) . '/../lib/Image/GifColorConverter.php';

$converter = new GifColorConverter;
$converter->put('0.gif', '0_converted.gif', array(0x000000 => 0xff0000, 0xffffff => 0xcccccc)); 

$converter->put('1.gif', '1_converted.gif', array(0x000000 => 0x0099ff)); 
