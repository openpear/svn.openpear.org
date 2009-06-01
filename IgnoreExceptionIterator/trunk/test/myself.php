<?php
include_once(dirname(__FILE__) . '/t/t.php');

$lime = new lime_test;
$it = new IgnoreExceptionIterator(new EmptyIterator());
$lime->isa_ok($it, 'IgnoreExceptionIterator',
              'Checks the class of an iterator');
$lime->isa_ok($it->getInnerIterator(), 'EmptyIterator',
              'Checks the class of an inner iterator');
$lime->ok($it instanceof Iterator, 'Checks Iterator interface');
