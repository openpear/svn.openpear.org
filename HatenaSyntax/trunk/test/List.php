<?php
include_once dirname(__FILE__) . '/t/t.php';

$lime = new lime_test;
$p = HatenaSyntax_Locator::it()->list;

//--
$context = PEG::context('-h');
list(list($result)) = $p->parse($context)->getData();
$lime->is($result, '-');