<?php
include_once dirname(__FILE__) . '/t/t.php';

$lime = new lime_test;

$parser = PEG::not(PEG::token('hoge'));

$lime->is('fuga', $parser->parse(PEG::context('fuga')));
try {
    $parser->parse(PEG::context('hoge'));
    $lime->fail();
} catch (PEG_Failure $e) {
    $lime->pass();
}