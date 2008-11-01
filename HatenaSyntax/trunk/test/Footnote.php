<?php
include_once dirname(__FILE__) . '/t/t.php';

$lime = new lime_test;

$footnote = HatenaSyntax_Footnote::getInstance();

$lime->is($footnote->parse($context = context('((hoge))')),
          array('type' => 'footnote', 'body' => 'hoge'));
$lime->is($footnote->parse($context = context('(((hoge))')),
          array('type' => 'footnote', 'body' => '(hoge'));
$lime->is($footnote->parse($context = context('((hoge)))')),
          array('type' => 'footnote', 'body' => 'hoge'));
$lime->is($footnote->parse($context = context('((hog)e))')),
          array('type' => 'footnote', 'body' => 'hog)e'));
try {
    $footnote->parse(context("((ho\nge))"));
    $lime->fail();
} catch (PEG_Failure $e) {
    $lime->pass();
}