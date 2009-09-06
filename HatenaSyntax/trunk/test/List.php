<?php
include_once dirname(__FILE__) . '/t/t.php';

$lime = new lime_test;
$p = HatenaSyntax_Locator::it()->list;

//--

$context = PEG::context('-h');
list($item) = $p->parse($context)->getData()->getChildren();
$data = $item->getValue();
$lime->is($data[0], '-');
$lime->is($data[1], array('h'));

//--

$context = PEG::context("-a\n-+b");
list($a) = $p->parse($context)->getData()->getChildren();
$lime->is($a->getValue(), array('-', array('a')));
list($b) = $a->getChildren();
$lime->is($b->getValue(), array('+', array('b')));