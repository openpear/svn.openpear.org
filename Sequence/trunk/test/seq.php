<?php
include_once dirname(__FILE__) . '/tool/lime.php';
include_once dirname(__FILE__) . '/../src/seq.php';
$lime = new lime_test;

////////////////////////////////////////////////////////////////////////////////

$lime->comment('count');
$seq = seq();
$lime->is($seq->length, 0);
$lime->is($seq->count(), 0);
$lime->is(count($seq), 0);

////////////////////////////////////////////////////////////////////////////////

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
unset($seq[1]);
$lime->is($seq->toArray(), array(1, 3, 4, 10));
$lime->is(count($seq), 4);

////////////////////////////////////////////////////////////////////////////////

$lime->comment('pop');
$seq = seq(1, 2, 3, 4);
$lime->is($seq->pop(), 4);
$seq->pop();
$seq->pop();
$seq->pop();
$lime->is($seq->toArray(), array());
try {
    $seq->pop();
    $lime->fail();
}
catch (RunTimeException $e) {
    $lime->pass();
}

////////////////////////////////////////////////////////////////////////////////

$lime->comment('reverse');
$seq = seq(1, 2, 3, 4);
$lime->is($seq->reverse()->toArray(), array(4, 3, 2, 1));

////////////////////////////////////////////////////////////////////////////////

$lime->comment('lengthen');
$seq = seq(1, 2, 3);
$seq->lengthen(2);
$lime->is(count($seq), 2);
$seq->lengthen(4);
$lime->is(count($seq), 4);
$lime->is($seq[-1], null);

////////////////////////////////////////////////////////////////////////////////

$lime->comment('tovar');
$seq = seq(1, 2, 3);
$seq->tovar($a, $b, $c);
$lime->is(array($a, $b, $c), array(1, 2, 3));

////////////////////////////////////////////////////////////////////////////////

$lime->comment('in');
$seq = seq(1, 2, 3);
$lime->ok($seq->in(0));
$lime->ok($seq->in(2));
$lime->ok(!$seq->in(3));
$lime->ok($seq->in(-1));
$lime->ok($seq->in(-3));
$lime->ok(!$seq->in(-4));

////////////////////////////////////////////////////////////////////////////////

$lime->comment('cut');
$seq = seq(1, 2, 3);
list($left, $right) = $seq->cut(0);
$lime->is($left->toArray(), array());
$lime->is($right->toArray(), array(1, 2, 3));
list($left, $right) = $seq->cut(3);
$lime->is($left->toArray(), array(1, 2, 3));
$lime->is($right->toArray(), array());
$seq = seq();
try {
    $seq->cut(0);
    $lime->fail();
}
catch (Exception $e) {
    $lime->pass();
}

////////////////////////////////////////////////////////////////////////////////

$lime->comment('unclip');
$seq = seq(1);
list($left, $right) = $seq->unclip();
$lime->is($left, 1);
$lime->is($right->toArray(), array());
try {
    seq()->cut(0);
    $lime->fail();
}
catch (Exception $e) {
    $lime->pass();
}

////////////////////////////////////////////////////////////////////////////////

$lime->comment('slice');
$seq = seq(1, 2, 3);
$lime->is($seq->slice(0)->toArray(), array(1, 2, 3));
$lime->is($seq->slice(0, 1)->toArray(), array(1));
$lime->is($seq->slice(2)->toArray(), array(3));
$lime->is($seq->slice(0, 3)->toArray(), array(1, 2, 3));
$lime->is($seq->slice(0, 4)->toArray(), array(1, 2, 3));
$lime->is($seq->slice(-1, 1)->toArray(), array(3));
$lime->is($seq->slice(-3)->toArray(), array(1, 2, 3));
try {
    $seq->slice(-10);
    $lime->fail();
}
catch (Exception $e) {
    $lime->pass();
}

////////////////////////////////////////////////////////////////////////////////

$lime->comment('rest');
$seq = seq(1);
$lime->is($seq->rest()->toArray(), array());
try {
    seq()->rest();
    $lime->fail();
}
catch (Exception $e) {
    $lime->pass();
}