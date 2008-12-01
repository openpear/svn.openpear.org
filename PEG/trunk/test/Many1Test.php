<?php
include_once dirname(__FILE__) . '/t/t.php';

$lime = new lime_test;
$many1 = PEG::many1(PEG::token('hoge'));

$lime->is($many1->parse(PEG::context('hoge')), array('hoge'));
try {
    $many1->parse(PEG::context(''));
    $lime->fail();
} catch (PEG_Failure $e) {
    $lime->pass();
}
$lime->is(array('hoge', 'hoge'), $many1->parse(PEG::context('hogehoge')));