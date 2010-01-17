<?php
include_once dirname(__FILE__) . '/t/t.php';

$lime = new lime_test;
$pre = HatenaSyntax_Locator::it()->pre;

//--

$context = PEG::context(">|\nh\n|<");
list($result) = $pre->parse($context)->getData();
$lime->is($result, array('h'));

$context = PEG::context(">|\nha|<");
list($result) = $pre->parse($context)->getData();
$lime->is($result, array('ha'));

$context = PEG::context(">|\n\n|<");
$lime->isnt($pre->parse($context), PEG::failure());