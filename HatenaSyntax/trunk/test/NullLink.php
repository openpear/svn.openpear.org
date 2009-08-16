<?php
include_once dirname(__FILE__) . '/t/t.php';

$lime = new lime_test;
$parser = HatenaSyntax_Locator::it()->link;

//--

$context = PEG::context('[]fugahoge[]');
$result = $parser->parse($context);
$lime->is($result, 'fugahoge');