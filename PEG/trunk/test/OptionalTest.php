<?php
include_once dirname(__FILE__) . '/t/t.php';

$lime = new lime_test;

$optional = new PEG_Optional(token('hoge'));
$lime->is($optional->parse(new PEG_Context('fuga')), false);
$lime->is($optional->parse(new PEG_Context('hoge')), 'hoge');