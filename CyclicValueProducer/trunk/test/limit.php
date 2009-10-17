<?php
include_once(dirname(__FILE__) . '/t/t.php');

$lime = new lime_test(3, new lime_output_color);

/* ============================== */

$lime->diag("checking option: range");

$it = new CyclicValueProducer(array(1,2,3), 5);

$output = array();
foreach ($it as $values) {
  $output[] = $values;
}
$lime->is(sizeof($output), 5, 'array size');
$lime->is($output, array(1,2,3,1,2), 'array values');

/* ============================== */

$lime->diag("Exception");

try {
  $it = new CyclicValueProducer('123');
  $lime->fail('failed to catch exception.');
}
catch (InvalidArgumentException $e) {
  $lime->pass('caught exception: '. $e->getMessage());
}
