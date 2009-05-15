<?php
include_once dirname(__FILE__) . '/t/t.php';


$t = new lime_test;
$p = PEG::ref($p_ref);
$p_ref = PEG::token('hoge');
$t->is($p->parse(PEG::context('hoge')), 'hoge');