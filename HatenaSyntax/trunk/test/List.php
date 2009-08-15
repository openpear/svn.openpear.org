<?php
include_once dirname(__FILE__) . '/t/t.php';

$lime = new lime_test;
$p = HatenaSyntax_Locator::it()->list;

//--

$context = PEG::context('-h');
list(list($type, $body)) = $result = $p->parse($context)->getData();
$lime->is($type, '-');
$lime->is($body, array('h'));

//--

$context = PEG::context("-a\n+-b");
list(, list(list($type, $body))) = $p->parse($context)->getData();
$lime->is($type, '-');
$lime->is($body, array('b'));