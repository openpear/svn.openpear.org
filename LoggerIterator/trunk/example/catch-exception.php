<?php
require_once('LoggerIterator.php');

$it = new LoggerIterator(new EmptyIterator);

try {
  var_dump($it->current());
} catch (Exception $e) {
  // do nothing
}
try {
  var_dump($it->key());
} catch (Exception $e) {
  // do nothing
}
