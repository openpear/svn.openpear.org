<?php
include_once dirname(__FILE__) . '/t/t.php';

$lime = new lime_test;
$parser = HatenaSyntax_Locator::it()->link;

//--

$context = PEG::context('[http://example.com/:image]');
$result = $parser->parse($context);
$lime->is($result->getType(), 'imagelink');
$lime->is($result->getData(), 'http://example.com/');