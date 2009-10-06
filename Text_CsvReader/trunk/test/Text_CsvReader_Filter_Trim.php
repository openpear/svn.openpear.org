<?php
include_once(dirname(__FILE__) . '/t/t.php');

$lime = new lime_test(16, new lime_output_color);

$input = array(array(" 1"," 2x "),
               array(3,"2x"),
               array(" 1",5),
               );

$output1 = array();
$it = new Text_CsvReader_Filter_Trim(new ArrayIterator($input));
foreach ($it as $result) {
  $output1[] = $result;
}

$output2 = array();
$it = new Text_CsvReader_Filter_Trim(new ArrayIterator($input),
                                         array('target'=>array(0,1)));
foreach ($it as $result) {
  $output2[] = $result;
}

$output3 = array();
$it = new Text_CsvReader_Filter_Trim(new ArrayIterator($input),
                                         array('target'=>array(0)));
foreach ($it as $result) {
  $output3[] = $result;
}

$output4 = array();
$it = new Text_CsvReader_Filter_Trim(new ArrayIterator($input),
                                         array('target'=>array(1)));
foreach ($it as $result) {
  $output4[] = $result;
}

//--

$lime->ok(sizeof($output1) === 3, 'array size: '. sizeof($output1));
$lime->ok($output1[0] === array("1","2x"), '0th element');
$lime->ok($output1[1] === array("3","2x"), '1st element');
$lime->ok($output1[2] === array("1","5"), '2nd element');

$lime->ok(sizeof($output2) === 3, 'array size: '. sizeof($output2));
$lime->ok($output2[0] === array("1","2x"), '0th element');
$lime->ok($output2[1] === array("3","2x"), '1st element');
$lime->ok($output2[2] === array("1","5"), '2nd element');

$lime->ok(sizeof($output3) === 3, 'array size: '. sizeof($output3));
$lime->ok($output3[0] === array("1"," 2x "), '0th element');
$lime->ok($output3[1] === array("3","2x"), '1st element');
$lime->ok($output3[2] === array("1",5), '2nd element');

$lime->ok(sizeof($output4) === 3, 'array size: '. sizeof($output4));
$lime->ok($output4[0] === array(" 1","2x"), '0th element');
$lime->ok($output4[1] === array(3,"2x"), '1st element');
$lime->ok($output4[2] === array(" 1","5"), '2nd element');

