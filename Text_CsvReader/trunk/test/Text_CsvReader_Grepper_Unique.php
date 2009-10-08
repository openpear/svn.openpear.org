<?php
include_once(dirname(__FILE__) . '/t/t.php');

$lime = new lime_test(15, new lime_output_color);

$input = array(array(" 1"," 2x "),
               array(3,4),
               array(3,"2x"),
               array(3,4.0),
               array(" 1",5),
               array(3,5),
               );


$output = array();
$it = new Text_CsvReader_Grepper_Unique(new ArrayIterator($input),
                                         array('target'=>array(0,1)));
foreach ($it as $result) {
  $output[] = $result;
}

$lime->ok(sizeof($output) === 5, 'array size: '. sizeof($output));
$lime->ok($output[0] === array(" 1"," 2x "), '0th element');
$lime->ok($output[1] === array(3,4), '1st element');
$lime->ok($output[2] === array(3,"2x"), '2nd element');
$lime->ok($output[3] === array(" 1",5), '3rd element');
$lime->ok($output[4] === array(3,5), '4th element');

/* ============================== */

$output = array();
$it = new Text_CsvReader_Grepper_Unique(new ArrayIterator($input),
                                         array('target'=>array(0)));
foreach ($it as $result) {
  $output[] = $result;
}

$lime->ok(sizeof($output) === 2, 'array size: '. sizeof($output));
$lime->ok($output[0] === array(" 1"," 2x "), '0th element');
$lime->ok($output[1] === array(3,4), '1st element');

/* ============================== */

$output = array();
$it = new Text_CsvReader_Grepper_Unique(new ArrayIterator($input),
                                         array('target'=>array(1)));
foreach ($it as $result) {
  $output[] = $result;
}

$lime->ok(sizeof($output) === 4, 'array size: '. sizeof($output));
$lime->ok($output[0] === array(" 1"," 2x "), '0th element');
$lime->ok($output[1] === array(3,4), '1st element');
$lime->ok($output[2] === array(3,"2x"), '2nd element');
$lime->ok($output[3] === array(" 1",5), '3rd element');

/* ============================== */

$lime->diag("Exception");

try {
  $it = new Text_CsvReader_Grepper_Unique(new ArrayIterator($input));
  $it->rewind();
  $lime->fail('required parameter not specified.');
}
catch (CsvReaderException $e) {
  $lime->pass('required parameter not specified.');
}
