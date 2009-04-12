<?php
include_once dirname(__FILE__) . '/t/t.php';

$lime = new lime_test;
$parser = PEG::lookaheadNot(PEG::token('hoge'));
$context = PEG::context('fuga');
$lime->is($parser->parse($context), 'fuga');
$lime->is($context->tell(), 0);

