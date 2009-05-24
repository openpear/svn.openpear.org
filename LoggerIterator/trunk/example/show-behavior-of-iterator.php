<?php

require_once('LoggerIterator.php');

$it = new LoggerIterator(new ArrayIterator(array(1,2)));
foreach ($it as $value) {
  var_dump($value);
}
