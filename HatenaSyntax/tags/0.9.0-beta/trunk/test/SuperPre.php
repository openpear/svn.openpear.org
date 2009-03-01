<?php
include_once dirname(__FILE__) . '/t/t.php';

$lime = new lime_test;
$parser = HatenaSyntax_Locator::it()->superPre;

//--

$context = context(">|hoge|\na||<");
$lime->is($parser->parse($context)->getData(), array('type' => 'hoge', 'body' => array('a')));

$context = context(">||\na\n||<");
$lime->is($parser->parse($context)->getData(), array('type' => '', 'body' => array('a')));