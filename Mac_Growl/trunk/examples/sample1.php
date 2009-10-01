<?php
require_once 'Mac/Growl.php';

$growl = new Mac_Growl('Mac_Growl_Example', array('Messages', 'Errors'));
$growl->register();
$growl->notify('Messages',
               'from ' . $growl->getApplicationName(),
               'Hello, Growl!');

echo $growl->getLastScript(), "\n";
