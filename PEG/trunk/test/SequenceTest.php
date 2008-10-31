<?php
include_once dirname(__FILE__) . '/t/t.php';

$lime = new lime_test;
$seq = new PEG_Sequence(array(token('h'), token('o')));
$context = new PEG_Context('ho');

$lime->is($seq->parse($context), array('h', 'o'));