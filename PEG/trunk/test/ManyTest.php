<?php
include_once dirname(__FILE__) . '/t/t.php';

$lime = new lime_test;

$many = new PEG_Many(token('a'));
$context = context('aaaaaaaa');
$lime->is($many->parse($context), array('a', 'a', 'a', 'a', 'a', 'a', 'a', 'a'));
$lime->is($context->tell(), 8);
