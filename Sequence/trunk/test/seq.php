<?php
include_once dirname(__FILE__) . '/tool/lime.php';
include_once dirname(__FILE__) . '/../code/Sequence.php';
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
$lime->is(seq(1, 2, 3)->rest()->toArray(), array(2, 3));
try {
    seq()->rest();
    $lime->fail();
}
catch (Exception $e) {
    $lime->pass();
}

////////////////////////////////////////////////////////////////////////////////

$lime->comment('halves');
$seq = seq(1, 2, 3);
list($left, $right) = $seq->halves();
$lime->is($left->toArray(), array(1));
$lime->is($right->toArray(), array(2, 3));

list($left, $right) = seq(1)->halves();
$lime->is($left->toArray(), array());
$lime->is($right->toArray(), array(1));

////////////////////////////////////////////////////////////////////////////////

$lime->comment('pick');
$seq = seq(1, 2, 3);
$lime->is($seq->pick(0)->nth(0), 1);
$lime->is($seq->pick(-1)->nth(0), 3);
try {
    $seq->pick(3);
    $lime->fail();
}
catch (Exception $e) {
    $lime->pass();
}
try {
    $seq->pick(-4);
    $lime->fail();
}
catch (Exception $e) {
    $lime->pass();
}

////////////////////////////////////////////////////////////////////////////////

$lime->comment('map');
$result = seq(1, 2, 3)->map(create_function('$v', 'return $v * 2;'))->toArray();
$lime->is($result, array(2, 4, 6));

////////////////////////////////////////////////////////////////////////////////

$lime->comment('filter');
$result = seq('h', '', 'fuga')->filter('strlen')->toArray();
$lime->is($result, array('h', 'fuga'));

////////////////////////////////////////////////////////////////////////////////

$lime->comment('reduce');
$result = seq(1, 2, 3, 4, 5)->reduce(create_function('$a, $b', 'return $a + $b;'));
$lime->is($result, 15);

////////////////////////////////////////////////////////////////////////////////

$lime->comment('all');
$result = seq(1, 2, 3, 4)->all('is_int');
$lime->ok($result);

////////////////////////////////////////////////////////////////////////////////

$lime->comment('any');
$result = seq(1, 2, 3, 4, '')->any('is_string');
$lime->ok($result);

////////////////////////////////////////////////////////////////////////////////

$lime->comment('each');

////////////////////////////////////////////////////////////////////////////////

$lime->comment('shift');
$seq = seq(1, 2, 3);
$lime->is($seq->shift(), 1);
$lime->is(count($seq), 2);

////////////////////////////////////////////////////////////////////////////////

$lime->comment('unshift');
$seq = seq(1, 2, 3);
$seq->unshift(0);
$lime->is($seq[0], 0);
$lime->is(count($seq), 4);

////////////////////////////////////////////////////////////////////////////////

$lime->comment('indexes');
$seq = seq(1, 2, 3);
$lime->ok(count($seq) === count($seq->indexes()));
$lime->is($seq->indexes()->toArray(), array(0, 1, 2));

////////////////////////////////////////////////////////////////////////////////

$lime->comment('repeat');
$seq = seq(1, 2, 3);
$lime->is($seq->repeat(1)->toArray(), array(1, 2, 3));
$lime->is($seq->repeat(2)->toArray(), array(1, 2, 3, 1, 2, 3));
try {
    $seq->repeat(0);
    $lime->fail();
}
catch (Exception $e) {
    $lime->pass();
}
try {
    $seq->repeat(-2);
    $lime->fail();
}
catch (Exception $e) {
    $lime->pass();
}

////////////////////////////////////////////////////////////////////////////////

$lime->comment('has');
$seq = seq(1, 2, 3);
$lime->ok($seq->has(1));
$lime->ok(!$seq->has(0));

////////////////////////////////////////////////////////////////////////////////

$lime->comment('harvest');
$seq = seq(1, 2, 3, false);
$lime->is($seq->harvest()->toArray(), array(1, 2, 3));

////////////////////////////////////////////////////////////////////////////////

$lime->comment('group');
$seq = seq(1, 2, 3, 4)->group(2);
$lime->is($seq[0]->toArray(), array(1, 2));
$lime->is($seq[1]->toArray(), array(3, 4));
try {
    seq(1, 2, 3, 4)->group(0);
    $lime->fail();
}
catch (Exception $e) {
    $lime->pass();
}

////////////////////////////////////////////////////////////////////////////////

$lime->comment('append');
$seq = seq(1, 2)->append(seq(3, 4));
$lime->is($seq->toArray(), array(1, 2, 3, 4));

////////////////////////////////////////////////////////////////////////////////

$lime->comment('concat');
$seq = seq(seq(1), seq(2, 3));
$lime->is($seq->concat()->toArray(), array(1, 2, 3));

////////////////////////////////////////////////////////////////////////////////

$lime->comment('head');
$seq = seq(1, 2, 3);
$lime->is($seq->head(1)->toArray(), array(1));
$lime->is($seq->head(3)->toArray(), array(1, 2, 3));
$lime->is($seq->head(4)->toArray(), array(1, 2, 3));
$lime->is($seq->head(0)->toArray(), array());
try {
    $seq->head(-1);
    $lime->fail();
}
catch (Exception $e) {
    $lime->pass();
}

////////////////////////////////////////////////////////////////////////////////

$lime->comment('tail');
$seq = seq(1, 2, 3);
$lime->is($seq->tail(1)->toArray(), array(3));
$lime->is($seq->tail(3)->toArray(), array(1, 2, 3));
$lime->is($seq->tail(4)->toArray(), array(1, 2, 3));
$lime->is($seq->tail(0)->toArray(), array());
try {
    $seq->tail(-1);
    $lime->fail();
}
catch (Exception $e) {
    $lime->pass();
}

////////////////////////////////////////////////////////////////////////////////

$lime->comment('zip');
$seq = seq(1, 2, 3);
$lime->is($seq->zip($seq)->nth(0)->toArray(), array(1, 1));
try {
    $seq->zip(seq(1));
    $lime->fail();
}
catch (Exception $e) {
    $lime->pass();
}

////////////////////////////////////////////////////////////////////////////////

$lime->comment('max');
$seq = seq(1, 2, 3);
$lime->is($seq->max(), 3);

////////////////////////////////////////////////////////////////////////////////

$lime->comment('min');
$seq = seq(1, 2, 3);
$lime->is($seq->min(), 1);

////////////////////////////////////////////////////////////////////////////////

$lime->comment('maxmin');
$seq = seq(1, 2, 3);
$lime->is($seq->maxmin()->toArray(), array(3, 1));

////////////////////////////////////////////////////////////////////////////////

$lime->comment('sum');
$seq = seq(1, 2, 3, 4, 5);
$lime->is($seq->sum(), 15);

////////////////////////////////////////////////////////////////////////////////

$lime->comment('suffix');
$seq = seq(1, 2, 3);
$lime->is($seq->suffix(4)->toArray(), array(1, 2, 3, 4));

////////////////////////////////////////////////////////////////////////////////

$lime->comment('prefix');
$seq = seq(1, 2, 3);
$lime->is($seq->prefix(0)->toArray(), array(0, 1, 2, 3));

////////////////////////////////////////////////////////////////////////////////

$lime->comment('trim');
$seq = seq(1, 1, 1, 2, 2, 3);
$lime->is($seq->trim(1, 3)->toArray(), array(2, 2));

////////////////////////////////////////////////////////////////////////////////

$lime->comment('fill');
$seq = seq(1, 2, 3);
$lime->is($seq->fill(0)->toArray(), array(0, 0, 0));

////////////////////////////////////////////////////////////////////////////////

$lime->comment('remove');
$seq = seq(1, 2, 3);
$lime->is($seq->remove(1, 3)->toArray(), array(2));

////////////////////////////////////////////////////////////////////////////////

$lime->comment('move');
$seq = seq(1, 2, 3);
$seq->move(0, -1);
$lime->is($seq->toArray(), array(1, 2, 1));

////////////////////////////////////////////////////////////////////////////////

$lime->comment('swap');
$seq = seq(1, 2, 3);
$seq->swap(0, -1);
$lime->is($seq->toArray(), array(3, 2, 1));

////////////////////////////////////////////////////////////////////////////////

$lime->comment('roll');
$seq = seq(1, 2, 3);
$seq->roll();
$lime->is($seq->toArray(), array(2, 3, 1));

////////////////////////////////////////////////////////////////////////////////

$lime->comment('rollback');
$seq = seq(1, 2, 3);
$seq->rollback();
$lime->is($seq->toArray(), array(3, 1, 2));

////////////////////////////////////////////////////////////////////////////////

$lime->comment('indices');
$seq = seq(1, 2, 1, 1, 2);
$lime->is($seq->indices(2)->toArray(), array(1, 4));
$lime->is($seq->indices(1)->toArray(), array(0, 2, 3));
$lime->is($seq->indices(4)->toArray(), array());
$lime->is($seq->indices()->toArray(), array());
$lime->is($seq->indices(1, 2)->toArray(), array(0, 1, 2, 3, 4));

////////////////////////////////////////////////////////////////////////////////

$lime->comment('interleave');
$seq = seq(1, 2, 3);
$lime->is($seq->interleave(0)->toArray(), array(1, 0, 2, 0, 3));
$lime->is(seq()->interleave(0)->toArray(), array());

////////////////////////////////////////////////////////////////////////////////

$lime->comment('flatten');
$lime->is(seq(1, 2, 3)->flatten()->toArray(), array(1, 2, 3));
$lime->is(seq(seq(1, 2, 3), seq(1, 2, 3))->flatten()->toArray(), array(1, 2, 3, 1, 2, 3));
