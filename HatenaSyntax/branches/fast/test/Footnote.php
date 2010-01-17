<?php
include_once dirname(__FILE__) . '/t/t.php';

$lime = new lime_test;
$footnote = HatenaSyntax_Locator::it()->footnote;

$buf = $footnote->parse(PEG::context('((a))'))->getData();
$lime->is($buf, array('a'));