<?php
include_once dirname(__FILE__) . '/t/t.php';

$lime = new lime_test;
$parser = new PEG_LookaheadNot(token('hoge'));
$context = context('fuga');
$lime->is(false, $parser->parse($context));
$lime->is($context->tell(), 0);

