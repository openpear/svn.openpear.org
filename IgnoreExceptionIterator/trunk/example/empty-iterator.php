<?php
require_once('IgnoreExceptionIterator.php');

$it = new IgnoreExceptionIterator(new EmptyIterator());
$it->current(); // returns null

$it = new EmptyIterator();
$it->current(); // throws exception
