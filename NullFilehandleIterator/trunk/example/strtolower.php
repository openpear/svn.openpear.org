<?php
require_once('NullFilehandleIterator.php');

$it = new NullFilehandleIterator();
foreach ($it as $line) {
  print strtolower($line);
}
