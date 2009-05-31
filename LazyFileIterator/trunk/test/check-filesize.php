<?php
include_once(dirname(__FILE__) . '/t/t.php');

$lime = new lime_test;
$it = new LazyFileIterator(__FILE__, "r");
$stat = stat(__FILE__);
$filesize = $stat['size'];

$bytes = 0;
foreach ($it as $line) {
  $bytes += strlen($line);
}
$lime->ok($bytes === $filesize);
foreach ($it as $line) {
  $bytes += strlen($line);
}
$lime->ok($bytes === $filesize*2);