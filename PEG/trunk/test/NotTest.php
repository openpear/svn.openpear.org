<?php
include_once dirname(__FILE__) . '/t/t.php';

$lime = new lime_test;

$parser = new PEG_Not(token('hoge'));

$lime->is('fuga', $parser->parse(context('fuga')));
try {
    $parser->parse(context('hoge'));
    $lime->fail();
} catch (PEG_Failure $e) {
    $lime->pass();
}