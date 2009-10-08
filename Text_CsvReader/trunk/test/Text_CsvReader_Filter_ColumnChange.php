<?php
include_once(dirname(__FILE__) . '/t/t.php');

$lime = new lime_test(6, new lime_output_color);

$input = array(array("abc","123","AtoZ"),
               array("234","xyz","1to9"),
               );

/* ============================== */

$lime->diag("checking option: column");

$it = new Text_CsvReader_Filter_ColumnChange(new ArrayIterator($input),
                                             array('column' => array(
                                                                     0 => 0,
                                                                     1 => 1,
                                                                     2 => 2,
                                                                     )));
$output = array();
foreach ($it as $result) {
  $output[] = $result;
}

$lime->ok(sizeof($output) === 2, 'array size: '. sizeof($output));
$lime->ok($output[0] === array("abc","123","AtoZ"), '0th element');
$lime->ok($output[1] === array("234","xyz","1to9"), '1st element');

/* ============================== */

$it = new Text_CsvReader_Filter_ColumnChange(new ArrayIterator($input),
                                             array('column' => array(
                                                                     0 => 2,
                                                                     1 => 2,
                                                                     2 => 1,
                                                                     3 => 0,
                                                                     )));
$output = array();
foreach ($it as $result) {
  $output[] = $result;
}

$lime->ok(sizeof($output) === 2, 'array size: '. sizeof($output));
$lime->ok($output[0] === array("AtoZ","AtoZ","123","abc"), '0th element');
$lime->ok($output[1] === array("1to9","1to9","xyz","234"), '1st element');

/* ============================== */

/*
$lime->diag("Exception: no option");

try {
  $it = new Text_CsvReader_Filter_ColumnChange(new ArrayIterator($input));
  $it->rewind();
  $lime->fail('required parameter not specified.');
}
catch (CsvReaderException $e) {
  $lime->pass('required parameter not specified.');
}
*/