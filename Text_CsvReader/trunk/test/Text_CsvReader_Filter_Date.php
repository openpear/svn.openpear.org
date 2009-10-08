<?php
include_once(dirname(__FILE__) . '/t/t.php');

$lime = new lime_test(5, new lime_output_color);

$input = array(array(0, 1230735600));

/* ============================== */

$lime->diag("checking option: target");

$it = new Text_CsvReader_Filter_Date(new ArrayIterator($input),
                                     array('target'=>1));
$output = array();
foreach ($it as $result) {
  $output[] = $result;
}
$lime->ok(sizeof($output) === 1, 'array size: '. sizeof($output));
$lime->ok($output[0] === array(0,"2009-01-01"), '0th element');

/* ============================== */

$it = new Text_CsvReader_Filter_Date(new ArrayIterator($input),
                                     array('target'=>array(0,1)));
$output = array();
foreach ($it as $result) {
  $output[] = $result;
}
$lime->ok(sizeof($output) === 1, 'array size: '. sizeof($output));
$lime->ok($output[0] === array("1970-01-01","2009-01-01"), '0th element');

/* ============================== */

$lime->diag("Exception: no option");

try {
  $it = new Text_CsvReader_Filter_Date(new ArrayIterator($input));
  $it->rewind();
  $lime->fail('required parameter not specified.');
}
catch (CsvReaderException $e) {
  $lime->pass('required parameter not specified.');
}

