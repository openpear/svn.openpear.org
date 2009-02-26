<?php

require_once '../NorfDocTest.php';

$group = NorfDocTestModuleGroup::defaultGroup();
$group->addModuleWithFile('fib.php');

$request = new NorfDocTestRequest('Fibonacci');
$group->executeRequest($request);


