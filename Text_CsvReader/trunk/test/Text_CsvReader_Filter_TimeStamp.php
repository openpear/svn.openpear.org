<?php
include_once(dirname(__FILE__) . '/t/t.php');

$lime = new lime_test(4, new lime_output_color);

$input = array(array(0, 1230735600));

/* ============================== */

$lime->diag("no option: filter all values");

$it = new Text_CsvReader_Filter_TimeStamp(new ArrayIterator($input));

$output = array();
foreach ($it as $result) {
  $output[] = $result;
}
$lime->ok(sizeof($output) === 1, 'array size: '. sizeof($output));
$lime->ok($output[0] === array("1970-01-01 09:00:00","2009-01-01 00:00:00"), '0th element');

/* ============================== */

$lime->diag("checking option: target");

$it = new Text_CsvReader_Filter_TimeStamp(new ArrayIterator($input),
                                          array('target'=>array(1)));
$output = array();
foreach ($it as $result) {
  $output[] = $result;
}
$lime->ok(sizeof($output) === 1, 'array size: '. sizeof($output));
$lime->ok($output[0] === array(0,"2009-01-01 00:00:00"), '0th element');
