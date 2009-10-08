<?php
include_once(dirname(__FILE__) . '/t/t.php');

$lime = new lime_test(16, new lime_output_color);

$input = array(array("abc","123"),
               array("345","abc"),
               array("2AB","XYZ8"),
               );

/* ============================== */

$lime->diag("checking option: pattern");

$it = new Text_CsvReader_Grepper_Regex(new ArrayIterator($input),
                                       array('pattern'=>array(0 => '/.*/',
                                                              1 => '/.*/')));
$output = array();
foreach ($it as $result) {
  $output[] = $result;
}

$lime->ok(sizeof($output) === 3, 'array size: '. sizeof($output));
$lime->ok($output[0] === array("abc","123"), '0th element');
$lime->ok($output[1] === array("345","abc"), '1st element');
$lime->ok($output[2] === array("2AB","XYZ8"), '2nd element');

/* ============================== */


$it = new Text_CsvReader_Grepper_Regex(new ArrayIterator($input),
                                       array('pattern'=>array(0 => '/^\d+$/')));
$output = array();
foreach ($it as $result) {
  $output[] = $result;
}

$lime->ok(sizeof($output) === 1, 'array size: '. sizeof($output));
$lime->ok($output[0] === array("345","abc"), '0th element');

/* ============================== */

$it = new Text_CsvReader_Grepper_Regex(new ArrayIterator($input),
                                       array('pattern'=>array(1 => '|[0-9]$|')));
$output = array();
foreach ($it as $result) {
  $output[] = $result;
}

$lime->ok(sizeof($output) === 2, 'array size: '. sizeof($output));
$lime->ok($output[0] === array("abc","123"), '0th element');
$lime->ok($output[1] === array("2AB","XYZ8"), '1st element');

/* ============================== */

$it = new Text_CsvReader_Grepper_Regex(new ArrayIterator($input),
                                       array('pattern'=>array(0 => '!\d([ab]{2}|4\d)!i',
                                                              1 => '/...+/')));
$output = array();
foreach ($it as $result) {
  $output[] = $result;
}

$lime->ok(sizeof($output) === 2, 'array size: '. sizeof($output));
$lime->ok($output[0] === array("345","abc"), '0th element');
$lime->ok($output[1] === array("2AB","XYZ8"), '1th element');

/* ============================== */

$lime->diag("Exception");

$it = new Text_CsvReader_Grepper_Regex(new ArrayIterator($input),
                                       array('pattern'=>array(0 => null, 1=>'')));
$output = array();
foreach ($it as $result) {
  $output[] = $result;
}

$lime->ok(sizeof($output) === 3, 'array size: '. sizeof($output));
$lime->ok($output[0] === array("abc","123"), '0th element');
$lime->ok($output[1] === array("345","abc"), '1st element');
$lime->ok($output[2] === array("2AB","XYZ8"), '2nd element');
