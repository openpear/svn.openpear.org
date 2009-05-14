<?php
include_once(dirname(__FILE__) . '/t/t.php');

$lime = new lime_test;
$strings1 = new Text_VariationProducer(Text_VariationProducer::UTF16);
$strings2 = new Text_VariationProducer(Text_VariationProducer::UTF8);
$strings3 = new Text_VariationProducer(Text_VariationProducer::SJIS);
$strings4 = new Text_VariationProducer(Text_VariationProducer::SJIS_WIN);
$strings5 = new Text_VariationProducer(Text_VariationProducer::EUCJP);
$strings6 = new Text_VariationProducer(Text_VariationProducer::EUCJP_WIN);
$strings7 = new Text_VariationProducer(Text_VariationProducer::CP51932);

//--

$lime->ok(iterator_count($strings1) === 63488);
$lime->ok(iterator_count($strings2) === 63488);
$lime->ok(iterator_count($strings3) === 127+94*94+63);
$lime->ok(iterator_count($strings4) === 127+94*120+63);
$lime->ok(iterator_count($strings5) === 127+94*94+94+94*94);
$lime->ok(iterator_count($strings6) === 127+94*94+94+94*94);
$lime->ok(iterator_count($strings7) === 127+94*94+94);
