<?php
include_once(dirname(__FILE__) . '/t/t.php');

$lime = new lime_test;
$strings01 = new Text_VariationProducer('[a]');
$strings02 = new Text_VariationProducer('[abc]');
$strings03 = new Text_VariationProducer('[a-c]');
$strings04 = new Text_VariationProducer('[-a-c]');
$strings05 = new Text_VariationProducer('[\]]');
$strings06 = new Text_VariationProducer('[\X]');
$strings07 = new Text_VariationProducer('[\n]');
$strings08 = new Text_VariationProducer('[\0]');
$strings09 = new Text_VariationProducer('[\00]');
$strings10 = new Text_VariationProducer('[\000]');
$strings11 = new Text_VariationProducer('[\x0]');
$strings12 = new Text_VariationProducer('[\x00]');
$strings13 = new Text_VariationProducer('[\0-\1]');
$strings14 = new Text_VariationProducer('[\00-\01]');
$strings15 = new Text_VariationProducer('[\000-\001]');
$strings16 = new Text_VariationProducer('[\x0-\x1]');
$strings17 = new Text_VariationProducer('[\x00-\x01]');
$strings18 = new Text_VariationProducer('[^\x02-\xff]');
$strings19 = new Text_VariationProducer('[^\2-\377]');
$strings20 = new Text_VariationProducer('[^]');
$strings21 = new Text_VariationProducer('[\0-\xff]');
$strings22 = new Text_VariationProducer('[0-9A-Za-z\0-\x2f\x3a-\x40\x5b-\x60\x7b-\xff]');
$strings23 = new Text_VariationProducer('[A-Z][0-9][0-9]');
$strings24 = new Text_VariationProducer('[\x5c\x5d]');

//--

$lime->ok(iterator_to_array($strings01) === array('a'));
$lime->ok(iterator_to_array($strings02) === array('a', 'b', 'c'));
$lime->ok(iterator_to_array($strings03) === array('a', 'b', 'c'));
$lime->ok(iterator_to_array($strings04) === array('-', 'a', 'b', 'c'));
$lime->ok(iterator_to_array($strings05) === array(']'));
$lime->ok(iterator_to_array($strings06) === array('X'));
$lime->ok(iterator_to_array($strings07) === array("\n"));
$lime->ok(iterator_to_array($strings08) === array("\0"));
$lime->ok(iterator_to_array($strings09) === array("\0"));
$lime->ok(iterator_to_array($strings10) === array("\0"));
$lime->ok(iterator_to_array($strings11) === array("\0"));
$lime->ok(iterator_to_array($strings12) === array("\0"));
$lime->ok(iterator_to_array($strings13) === array("\0", "\1"));
$lime->ok(iterator_to_array($strings14) === array("\0", "\1"));
$lime->ok(iterator_to_array($strings15) === array("\0", "\1"));
$lime->ok(iterator_to_array($strings16) === array("\0", "\1"));
$lime->ok(iterator_to_array($strings17) === array("\0", "\1"));
$lime->ok(iterator_to_array($strings18) === array("\0", "\1"));
$lime->ok(iterator_to_array($strings19) === array("\0", "\1"));
$lime->ok(iterator_to_array($strings20) === iterator_to_array($strings21));
$lime->ok(iterator_to_array($strings21) === iterator_to_array($strings22));
$lime->ok(iterator_count($strings22) == 256);
$lime->ok(iterator_count($strings23) == 2600);
$lime->ok(iterator_to_array($strings24) === array('\\', ']'));
