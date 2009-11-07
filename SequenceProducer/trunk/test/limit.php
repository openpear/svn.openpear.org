<?php
include_once(dirname(__FILE__) . '/t/t.php');

$lime = new lime_test(4, new lime_output_color);

/* ============================== */

$lime->diag("checking option: range");

$it = new SequenceProducer(1, 5);

/*
// for debug
require('LoggerIterator.php');
$it = new LoggerIterator($it, LoggerIterator::VERBOSE);
*/

$output = array();
foreach ($it as $values) {
  $output[] = $values;
}
$lime->is(sizeof($output), 5, 'array size');
$lime->is($output, array(1,2,3,4,5), 'array values');

/* ============================== */

$it = new LimitIterator(new SequenceProducer(10), 0, 20);
$output = null;
foreach ($it as $values) {
  $output = $values;
}
$lime->is($output, 29, '20th element of the sequence from 10');

/* ============================== */

$lime->diag("Exception");

try {
  $it = new SequenceProducer(3, 2);
  $lime->fail('failed to catch exception.');
}
catch (InvalidArgumentException $e) {
  $lime->pass('caught exception: '. $e->getMessage());
}
