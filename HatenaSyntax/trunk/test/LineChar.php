<?php
include_once dirname(__FILE__) . '/t/t.php';

$lime = new lime_test;

$lineitem = HatenaSyntax_LineChar::getInstance();

$lime->is('a', $lineitem->parse(context('a')));
try {
    $lineitem->parse(context("\n"));
    $lime->fail();
} catch (PEG_Failure $e) {
    $lime->pass();
}