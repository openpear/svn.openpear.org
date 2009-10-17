<?php
include_once(dirname(__FILE__) . '/t/t.php');

$t = new lime_test(12, new lime_output_color);

$t->diag('ArrayIterator with empty array');

$it = new LoggerIterator(new ArrayIterator(array()), LoggerIterator::VERBOSE);

$output = array();
ob_start();
foreach ($it as $key=>$value) {
  $output[] = $value;
}
$log = ob_get_clean();

$t->is($output, array());
$t->ok(preg_match('/ArrayIterator::rewind/',  $log), 'rewind()');
$t->ok(preg_match('/ArrayIterator::valid/',   $log), 'valid()');
$t->ok(!preg_match('/ArrayIterator::current/', $log), 'no current()');
$t->ok(!preg_match('/ArrayIterator::key/',     $log), 'no key()');
$t->ok(!preg_match('/ArrayIterator::next/',    $log), 'no next()');

/* ============================== */

$t->diag('ArrayIterator with 1 element array');

$it = new LoggerIterator(new ArrayIterator(array(1)), LoggerIterator::VERBOSE);

$output = array();
ob_start();
foreach ($it as $key=>$value) {
  $output[] = $value;
}
$log = ob_get_clean();

$t->is($output, array(1));
$t->ok(preg_match('/ArrayIterator::rewind/',  $log), 'rewind()');
$t->ok(preg_match('/ArrayIterator::valid/',   $log), 'valid()');
$t->ok(preg_match('/ArrayIterator::current/', $log), 'current()');
$t->ok(preg_match('/ArrayIterator::key/',     $log), 'key()');
$t->ok(preg_match('/ArrayIterator::next/',    $log), 'next()');
