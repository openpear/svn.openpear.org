<?php
include_once(dirname(__FILE__) . '/t/t.php');

$t = new lime_test(2, new lime_output_color);

$it = new LoggerIterator(new EmptyIterator);

$t->diag('Exception by EmptyIterator::current()');

try {
  ob_start();
  $it->current() == null;
  $output = ob_get_clean();
  $t->fail('failed to catch exception.');
} catch (Exception $e) {
  $output = ob_get_clean();
  $t->pass('caught exception: '. $e->getMessage());
}
$t->ok(preg_match('/^Caught Exception: /', $output), 'LoggerIterator output');
