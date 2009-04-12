<?php
include_once dirname(__FILE__) . '/t/t.php';

$lime = new lime_test;
$elt = HatenaSyntax_Locator::it()->lineElement;

//--
$context = PEG::context('a');
$lime->is($elt->parse($context), 'a');
