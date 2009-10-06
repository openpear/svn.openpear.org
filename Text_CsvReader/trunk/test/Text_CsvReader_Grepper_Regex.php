<?php
include_once(dirname(__FILE__) . '/t/t.php');

$lime = new lime_test(12, new lime_output_color);

$input = array(array("abc","123"),
               array("345","abc"),
               array("2AB","XYZ8"),
               );

$output1 = array();
$it = new Text_CsvReader_Grepper_Regex(new ArrayIterator($input),
                                       array('pattern'=>array(0 => '/.*/',
                                                            1 => '/.*/')));
foreach ($it as $result) {
  $output1[] = $result;
}

$output2 = array();
$it = new Text_CsvReader_Grepper_Regex(new ArrayIterator($input),
                                       array('pattern'=>array(0 => '/^\d+$/')));


foreach ($it as $result) {
  $output2[] = $result;
}

$output3 = array();
$it = new Text_CsvReader_Grepper_Regex(new ArrayIterator($input),
                                       array('pattern'=>array(1 => '|[0-9]$|')));
foreach ($it as $result) {
  $output3[] = $result;
}

$output4 = array();
$it = new Text_CsvReader_Grepper_Regex(new ArrayIterator($input),
                                       array('pattern'=>array(0 => '!\d([ab]{2}|4\d)!i',
                                                              1 => '/...+/')));
foreach ($it as $result) {
  $output4[] = $result;
}

//--

$lime->ok(sizeof($output1) === 3, 'array size: '. sizeof($output1));
$lime->ok($output1[0] === array("abc","123"), '0th element');
$lime->ok($output1[1] === array("345","abc"), '1st element');
$lime->ok($output1[2] === array("2AB","XYZ8"), '2nd element');

$lime->ok(sizeof($output2) === 1, 'array size: '. sizeof($output2));
$lime->ok($output2[0] === array("345","abc"), '0th element');

$lime->ok(sizeof($output3) === 2, 'array size: '. sizeof($output3));
$lime->ok($output3[0] === array("abc","123"), '0th element');
$lime->ok($output3[1] === array("2AB","XYZ8"), '1st element');

$lime->ok(sizeof($output4) === 2, 'array size: '. sizeof($output4));
$lime->ok($output4[0] === array("345","abc"), '0th element');
$lime->ok($output4[1] === array("2AB","XYZ8"), '1th element');

