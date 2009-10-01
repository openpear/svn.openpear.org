<?php
require_once 'Mac/Growl.php';

$growl = new Mac_Growl('Mac_Growl_Example', array('Messages', 'Errors'));
//$growl->register();
$growl->notify('Messages',
               'from ' . $growl->getApplicationName(),
               'sticky=true, priority=1 and specifying an icon',
               array(
                   'sticky' => true,
                   'priority' => 1,
                   'icon' => dirname(__FILE__) . '/icon.png',
               ));

echo $growl->getLastScript(), "\n";
