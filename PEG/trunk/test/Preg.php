<?php
include_once dirname(__FILE__) . '/t/t.php';

$t = new lime_test;


$context = PEG::context('aabb');
$t->is(PEG::preg('#a+#')->parse($context), 'aa');
$t->is($context->tell(), 2);
$t->is(PEG::preg('#b+#')->parse($context), 'bb');
$t->ok($context->eos());