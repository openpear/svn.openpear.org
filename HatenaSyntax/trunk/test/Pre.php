<?php
include_once dirname(__FILE__) . '/t/t.php';

$lime = new lime_test;
$pre = HatenaSyntax_Locator::it()->pre;

//--

$context = context(">|\nh\n|<");
$lime->is(array_val($pre->parse($context)->getData(), 0), array('h'));

$context = context(">|\nha|<");
$lime->is(array_val($pre->parse($context)->getData(), 0), array('ha'));