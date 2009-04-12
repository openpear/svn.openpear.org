<?php
include_once dirname(__FILE__) . '/t/t.php';

$lime = new lime_test;

$lime->is(false, ref(PEG_EOS::getInstance())->parse(PEG::context('')));

$context = PEG::context('hoge');
$context->read(4);

$lime->is(false, ref(PEG::eos())->parse($context));