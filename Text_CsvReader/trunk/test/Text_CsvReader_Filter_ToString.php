<?php
include_once(dirname(__FILE__) . '/t/t.php');

$lime = new lime_test(4, new lime_output_color);

$input = array(array(" 1 "," 2 "), array(3,4), array("5","\n6"));
$output = array();

$producer = new ArrayIterator($input);
$it = new Text_CsvReader_Filter_ToString($producer);
foreach ($it as $result) {
  $output[] = $result;
}

//--

$lime->ok(sizeof($output) === 3, 'array size: '. sizeof($output));
$lime->ok($output[0] === array(" 1 "," 2 "), '0th element');
$lime->ok($output[1] === array("3","4"), '1st element');
$lime->ok($output[2] === array("5","\n6"), '2nd element');
