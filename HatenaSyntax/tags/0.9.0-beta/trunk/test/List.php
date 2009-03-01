<?php
include_once dirname(__FILE__) . '/t/t.php';

$lime = new lime_test;
$p = HatenaSyntax_Locator::it()->list;

//--
$context = context('-h');
$lime->is(array_val(array_val($p->parse($context)->getData(), 0), 0), '-');