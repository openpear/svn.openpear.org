<?php
include_once dirname(__FILE__) . '/t/t.php';

$lime = new lime_test;

$p = new PEG_FlexibleMany(PEG::token('a'), PEG::token('a'));
$c = PEG::context('aaaa');

$lime->is($p->parse($c), array(array('a', 'a', 'a'), 'a'));

$p = PEG::fmany('a', 'ab');
$c = PEG::context('aaab');

$lime->is($p->parse($c), array(array('a', 'a'), 'ab'));
