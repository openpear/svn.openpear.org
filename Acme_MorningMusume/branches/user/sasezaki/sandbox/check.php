<?php

set_include_path(dirname(__DIR__) . '/library' . PATH_SEPARATOR . get_include_path());
 
 
require_once 'Zend/Loader/Autoloader.php';
Zend_Loader_Autoloader::getInstance()->setFallbackAutoloader(true);

class Test implements Acme_MorningMusume_Person_CurrentMemberInterface
{
}
