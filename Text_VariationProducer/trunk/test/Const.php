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
$lime->ok(iterator_count($strings3) === 8995);
$lime->ok(iterator_count($strings4) === 11439);
$lime->ok(iterator_count($strings5) === 17861);
$lime->ok(iterator_count($strings6) === 17861);
$lime->ok(iterator_count($strings7) === 9025);

