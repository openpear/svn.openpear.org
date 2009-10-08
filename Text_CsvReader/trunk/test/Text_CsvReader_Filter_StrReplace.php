<?php
include_once(dirname(__FILE__) . '/t/t.php');

$lime = new lime_test(7, new lime_output_color);

$input = array(array("abc","123","AtoZ"),
               array("234","xyz","1to9"),
               );

/* ============================== */

$lime->diag("checking option: from/to");

$it = new Text_CsvReader_Filter_StrReplace(new ArrayIterator($input),
                                           array('from'=>array(0 => 'to'),
                                                 'to'  =>array(0 => '2')));

$output = array();
foreach ($it as $result) {
  $output[] = $result;
}

$lime->ok(sizeof($output) === 2, 'array size: '. sizeof($output));
$lime->ok($output[0] === array("abc","123","AtoZ"), '0th element');
$lime->ok($output[1] === array("234","xyz","1to9"), '1st element');

/* ============================== */

$it = new Text_CsvReader_Filter_StrReplace(new ArrayIterator($input),
                                           array('from'=>array(2 => 'to'),
                                                 'to'  =>array(2 => '2')));
$output = array();
foreach ($it as $result) {
  $output[] = $result;
}

$lime->ok(sizeof($output) === 2, 'array size: '. sizeof($output));
$lime->ok($output[0] === array("abc","123","A2Z"), '0th element');
$lime->ok($output[1] === array("234","xyz","129"), '1st element');

/* ============================== */

$lime->diag("Exception");

try {
  $it = new Text_CsvReader_Filter_StrReplace(new ArrayIterator($input));
  $it->rewind();
  $lime->fail('required parameter not specified.');
}
catch (CsvReaderException $e) {
  $lime->pass('required parameter not specified.');
}


