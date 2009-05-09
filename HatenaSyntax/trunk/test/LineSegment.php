<?php
include_once dirname(__FILE__) . '/t/t.php';

$lime = new lime_test;
$elt = HatenaSyntax_Locator::it()->lineSegment;

//--
$context = PEG::context('');
$lime->is($elt->parse($context), array());

