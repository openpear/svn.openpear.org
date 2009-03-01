<?php
include_once dirname(__FILE__) . '/t/t.php';

$lime = new lime_test();
$definition = HatenaSyntax_Locator::it()->definition;

//--

$context = context("::a\r\n");
$buf = $definition->parse($context);
$lime->is($buf[0], array());
$lime->is($buf[1], array('a'));
$lime->ok($context->eos());