<?php
include_once dirname(__FILE__) . '/tool/lime.php';
include_once dirname(__FILE__) . '/../src/seq.php';
$lime = new lime_test;


$lime->comment('count');
$seq = seq();
$lime->is($seq->length, 0);
$lime->is($seq->count(), 0);
$lime->is(count($seq), 0);


$lime->comment('ArrayAccess');
$seq = seq(1, 2, 3, 4, 5);
$lime->is($seq[0], 1);
$lime->is($seq[-1], 5);
$lime->ok(isset($seq[2]));
$lime->ok(!isset($seq[5]));
$seq[4] = 10;
$lime->is($seq[4], 10);
$seq[] = 11;
$lime->is($seq[-1], 11);
unset($seq[-1]);
$lime->ok(isset($seq[-1]));
$lime->is($seq->toArray(), array(1, 2, 3, 4, 10));
$lime->is(count($seq), 5);