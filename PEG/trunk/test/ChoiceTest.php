<?php
include_once dirname(__FILE__) . '/t/t.php';

$t = new lime_test;
$choice = new PEG_Choice(array(token('hoge'), 
                               token('fuga')));
$t->is($choice->parse(new PEG_Context('hoge')), 'hoge');
$t->is($choice->parse($c = new PEG_Context('fuga')), 'fuga');
$t->is($c->tell(), 4);