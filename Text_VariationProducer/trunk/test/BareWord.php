<?php
include_once(dirname(__FILE__) . '/t/t.php');

$lime = new lime_test(18, new lime_output_color());
$strings1 = new Text_VariationProducer('\\');
$strings2 = new Text_VariationProducer('\0');
$strings3 = new Text_VariationProducer('\00');
$strings4 = new Text_VariationProducer('\000');
$strings5 = new Text_VariationProducer('\0000');
$strings6 = new Text_VariationProducer('\x');
$strings7 = new Text_VariationProducer('\x0');
$strings8 = new Text_VariationProducer('\x00');
$strings9 = new Text_VariationProducer('\x000');
$strings10 = new Text_VariationProducer('abc');
$strings11 = new Text_VariationProducer('\n');
$strings12 = new Text_VariationProducer('\{');
$strings13 = new Text_VariationProducer('\[');
$strings14 = new Text_VariationProducer('\x5b\x5c\x5d');
$strings15 = new Text_VariationProducer('[]');
$strings16 = new Text_VariationProducer('[\]');
$strings17 = new Text_VariationProducer('{}');
$strings18 = new Text_VariationProducer('{\}');

//--
$lime->diag('bare words');
$lime->is(iterator_to_array($strings1), array('\\'));
$lime->is(iterator_to_array($strings2), array("\0"));
$lime->is(iterator_to_array($strings3), array("\0"));
$lime->is(iterator_to_array($strings4), array("\0"));
$lime->is(iterator_to_array($strings5), array("\0"."0"));
$lime->is(iterator_to_array($strings6), array('x'));
$lime->is(iterator_to_array($strings7), array("\0"));
$lime->is(iterator_to_array($strings8), array("\0"));
$lime->is(iterator_to_array($strings9), array("\0"."0"));
$lime->is(iterator_to_array($strings10), array("abc"));
$lime->is(iterator_to_array($strings11), array("\n"));
$lime->is(iterator_to_array($strings12), array('{'));
$lime->is(iterator_to_array($strings13), array('['));
$lime->is(iterator_to_array($strings14), array('[\\]'));
$lime->is(iterator_to_array($strings15), array('[]'));
$lime->is(iterator_to_array($strings16), array('[]'));
$lime->is(iterator_to_array($strings17), array('{}'));
$lime->is(iterator_to_array($strings18), array('{}'));

