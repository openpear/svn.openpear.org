<?php
include_once dirname(__FILE__) . '/t/t.php';

$lime = new lime_test;
$parser = PEG::lookaheadNot(PEG::token('hoge'));
$context = PEG::context('fuga');
$lime->is(false, $parser->parse($context));
$lime->is($context->tell(), 0);

