<?php
require_once 'Mac/Growl.php';

$growl = new Mac_Growl('Mac_Growl_Example', array('Messages', 'Stickies'));
//$growl->register();
$growl->notify('Stickies',
               'from ' . $growl->getApplicationName(),
               'sticky=true, priority=1 and specifying an icon',
               array(
                   'sticky' => true,
                   'priority' => Mac_Growl::PRIORITY_HIGH,
                   'icon' => dirname(__FILE__) . '/icon.png',
               ));

echo $growl->getLastScript(), "\n";
