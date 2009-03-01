<?php
include_once dirname(__FILE__) . '/../code/PEG.php';
include_once 'Benchmark/Timer.php';

/**
 * メモ化の効果を見るサンプル
 */

$t = new Benchmark_Timer;
$str = '((((((((1))))))))';


$t->start();

$a = PEG::ref();
$p = PEG::ref();

$a->is(PEG::choice(
    PEG::seq($p, '+', $a),
    PEG::seq($p, '-', $a),
    $p
));
$p->is(PEG::choice(
    PEG::seq('(', $a, ')'),
    '1'
));

$a->parse(PEG::context($str));

$t->setMarker('no memoize');

$a = PEG::ref();
$p = PEG::ref();

$a->is(PEG::memo(PEG::choice(
    PEG::seq($p, '+', $a),
    PEG::seq($p, '-', $a),
    $p
)));
$p->is(PEG::memo(PEG::choice(
    PEG::seq('(', $a, ')'),
    '1'
)));

$a->parse(PEG::context($str));

$t->setMarker('memoize');
$t->stop();
$t->display();
