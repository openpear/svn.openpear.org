<?php
require_once('LazyFileIterator.php');

$inputs = new AppendIterator();

$files = $_SERVER['argv'];
array_shift($files); // remove filename of myself

if ($files === array()) {
  $files[] = 'php://stdin';
}
foreach($files as $filename) {
  $inputs->append(new LazyFileIterator($filename, 'r'));
}

foreach($inputs as $line) {
  print $line;
}
