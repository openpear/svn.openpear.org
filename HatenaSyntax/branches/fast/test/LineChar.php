<?php
include_once dirname(__FILE__) . '/t/t.php';

$t = new lime_test;
$char = HatenaSyntax_Locator::it()->lineChar;

//--
$context = PEG::context('');
$t->is($char->parse($context), PEG::failure());

