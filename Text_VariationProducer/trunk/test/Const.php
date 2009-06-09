<?php
include_once(dirname(__FILE__) . '/t/t.php');

$lime = new lime_test(8, new lime_output_color());
$strings1 = new Text_VariationProducer(Text_VariationProducer::UTF16);
$strings2 = new Text_VariationProducer(Text_VariationProducer::UTF8);
$strings3 = new Text_VariationProducer(Text_VariationProducer::SJIS);
$strings4 = new Text_VariationProducer(Text_VariationProducer::SJIS_WIN);
$strings5 = new Text_VariationProducer(Text_VariationProducer::EUCJP);
$strings6 = new Text_VariationProducer(Text_VariationProducer::EUCJP_WIN);
$strings7 = new Text_VariationProducer(Text_VariationProducer::CP51932);
$strings8 = new Text_VariationProducer(Text_VariationProducer::NOT_UTF_8);

//--

$lime->diag('constants');
$lime->is(iterator_count($strings1), 63488, "number of chars for UTF-16");
$lime->is(iterator_count($strings2), 63488, "number of chars for UTF-8");
$lime->is(iterator_count($strings3), 127+94*94+63, "number of chars for Shift_JIS");
$lime->is(iterator_count($strings4), 127+94*120+63, "number of chars for SJIS-win");
$lime->is(iterator_count($strings5), 127+94*94+94+94*94, "number of chars for EUC-JP");
$lime->is(iterator_count($strings6), 127+94*94+94+94*94, "number of chars for EUCJP-win");
$lime->is(iterator_count($strings7), 127+94*94+94, "number of chars for CP51932");
$lime->is(iterator_count($strings8), 256*256*256+256*256+256-63488-128*128-128*(30*64)*2-128*128*128, "number of 1-3byte strings which is not UTF-8");

