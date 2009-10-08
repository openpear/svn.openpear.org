<?php
include_once(dirname(__FILE__) . '/t/t.php');

$lime = new lime_test(12, new lime_output_color);

$input = array(array(" 1"," 2x "),
               array(3,4),
               array("5","\n6"),
               array("7x", "8e9"));


/* ============================== */

$output = array();
$it = new Text_CsvReader_Grepper_Numeric(new ArrayIterator($input),
                                         array('target'=>array(0,1)));
foreach ($it as $result) {
  $output[] = $result;
}

$lime->ok(sizeof($output) === 2, 'array size: '. sizeof($output));
$lime->ok($output[0] === array(3,4), '0th element');
$lime->ok($output[1] === array("5","\n6"), '1st element');

/* ============================== */

$output = array();
$it = new Text_CsvReader_Grepper_Numeric(new ArrayIterator($input),
                                         array('target'=>array(0)));
foreach ($it as $result) {
  $output[] = $result;
}

$lime->ok(sizeof($output) === 3, 'array size: '. sizeof($output));
$lime->ok($output[0] === array(" 1"," 2x "), '0th element');
$lime->ok($output[1] === array(3,4), '1st element');
$lime->ok($output[2] === array("5","\n6"), '2nd element');

/* ============================== */

$output = array();
$it = new Text_CsvReader_Grepper_Numeric(new ArrayIterator($input),
                                         array('target'=>array(1)));
foreach ($it as $result) {
  $output[] = $result;
}

$lime->ok(sizeof($output) === 3, 'array size: '. sizeof($output));
$lime->ok($output[0] === array(3,4), '0th element');
$lime->ok($output[1] === array("5","\n6"), '1st element');
$lime->ok($output[2] === array("7x","8e9"), '2nd element');

/* ============================== */

$lime->diag("Exception: no option");

try {
  $it = new Text_CsvReader_Grepper_Numeric(new ArrayIterator($input));
  $it->rewind();
  $lime->fail('required parameter not specified.');
}
catch (CsvReaderException $e) {
  $lime->pass('required parameter not specified.');
}
