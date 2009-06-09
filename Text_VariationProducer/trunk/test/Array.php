<?php
include_once(dirname(__FILE__) . '/t/t.php');

$lime = new lime_test(4, new lime_output_color());
$strings1 = new Text_VariationProducer(array('a'));
$strings2 = new Text_VariationProducer(array('a', '[b-d]'));
$strings3 = new Text_VariationProducer(array(''));
$strings4 = new Text_VariationProducer(array());

//--

$lime->diag('arrays');
$lime->ok(iterator_to_array($strings1) === array('a'));
$lime->ok(iterator_to_array($strings2) === array('a', 'b', 'c', 'd'));
$lime->ok(iterator_to_array($strings3) === array(''));
$lime->ok(iterator_to_array($strings4) === array());
