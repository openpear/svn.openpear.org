<?php
include_once(dirname(__FILE__) . '/t/t.php');

$lime = new lime_test(9, new lime_output_color);

$input = array(
               array(1,2),
               array(3,4),
               );

/* ============================== */

$lime->diag("checking option: range");

$it = new Text_CsvReader_Filter_Random(new ArrayIterator($input),
                                       array('range'=>
                                             array(0=>
                                                   array(5,6))));
$output = array();
foreach ($it as $values) {
  $output[] = $values;
}
$lime->ok(sizeof($output) === 2, 'array size: '. sizeof($output));
$lime->ok($output[0] === array(5,2) || $output[0] === array(6,2), '0th element');
$lime->ok($output[1] === array(5,4) || $output[1] === array(6,4), '1st element');

$it = new Text_CsvReader_Filter_Random(new ArrayIterator($input),
                                       array('range'=>
                                             array(0=>array(-100,-100),
                                                   1=>array(10,11))));
$output = array();
foreach ($it as $values) {
  $output[] = $values;
}
$lime->ok(sizeof($output) === 2, 'array size: '. sizeof($output));
$lime->ok($output[0] === array(-100,10) || $output[0] === array(-100,11), '0th element');
$lime->ok($output[1] === array(-100,10) || $output[1] === array(-100,11), '1st element');

/* ============================== */

$lime->diag("checking option: choice");

$it = new Text_CsvReader_Filter_Random(new ArrayIterator($input),
                                       array('choice'=>
                                             array(1=>
                                                   array('A','B'))));
$output = array();
foreach ($it as $values) {
  $output[] = $values;
}
$lime->ok(sizeof($output) === 2, 'array size: '. sizeof($output));
$lime->ok($output[0] === array(1,'A') || $output[0] === array(1,'B'), '0th element');
$lime->ok($output[1] === array(3,'A') || $output[1] === array(3,'B'), '1st element');

/* ============================== */

/*
$lime->diag("Exception");

try {
  $it = new Text_CsvReader_Filter_Random(new ArrayIterator($input));
  $it->rewind();
  $lime->fail('required parameter not specified.');
}
catch (CsvReaderException $e) {
  $lime->pass('required parameter not specified.');
}
*/
