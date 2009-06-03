<?php
include_once(dirname(__FILE__) . '/t/t.php');

$lime = new lime_test;
$orig_it = new ArrayIterator(array());
$it = new IgnoreExceptionIterator($orig_it);

$lime->ok($it->valid()   === false, 'valid()   returns false');
$lime->ok($it->current() === null,  'current() returns null');
$lime->ok($it->key()     === null,  'key()     returns null');
$lime->ok($it->next()    === null,  'next()    returns null');
$lime->ok($it->rewind()  === null,  'rewind()  returns null');

$lime->ok(iterator_count($it) === iterator_count($orig_it),
          'iterator has '.iterator_count($it). ' element(s)');
$lime->ok(iterator_to_array($it) === iterator_to_array($orig_it),
          'iterator has same elements with original one');
