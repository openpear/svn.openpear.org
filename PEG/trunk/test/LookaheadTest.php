<?php
include_once dirname(__FILE__) . '/t/t.php';

$lime = new lime_test;

$parser = PEG::lookahead(PEG::token('hoge'));
$context = PEG::context('hogehoge');

$lime->is($parser->parse($context), false);
$lime->is($context->tell(), 0);