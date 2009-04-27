<?php
include_once dirname(__FILE__) . '/t/t.php';

$t = new lime_test;

$parser = PEG::listof(PEG::preg('#[a-zA-Z0-9]+#'), PEG::preg('#\s*,\s*#'));
$context = PEG::context('abc, def, ghi');
$t->is($parser->parse($context), array('abc', 'def', 'ghi'));
$t->ok($context->eos());