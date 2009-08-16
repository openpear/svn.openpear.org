<?php
include_once dirname(__FILE__) . '/t/t.php';

$lime = new lime_test;
$parser = HatenaSyntax_Locator::it()->link;

//--

$context = PEG::context('[[keyword]]');
$result = $parser->parse($context);
$lime->is($result->getType(), 'relativelink');
$lime->is($result->getData(), 'keyword');