<?php
include_once dirname(__FILE__) . '/t/t.php';

$lime = new lime_test;
$p = new HatenaSyntax_SuperPreElement;

$c = context('
hogehoge');
$lime->is($p->parse($c), 'hogehoge');

$c = context("\nfuga||<");
$lime->is($p->parse($c), 'fuga');
$lime->is($c->tell(), 5);

try {
    $p->parse(context("\n||<"));
    $lime->fail();
} catch (PEG_Failure $e) {
    $lime->pass();
}