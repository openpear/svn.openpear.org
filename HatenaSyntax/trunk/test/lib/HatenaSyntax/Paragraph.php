<?php
include_once dirname(__FILE__) . '/../../t.php';

$t = new lime_test;

$p = new HatenaSyntax_Paragraph(PEG::anything());
$c = PEG::context(array(
    'abc',
    'def'
));

$t->is(
    $p->parse($c),
    array('abc')
);

$t->is(
    $p->parse($c),
    array('def')
);

$c = PEG::context(array('日本語'));
$t->is($p->parse($c), array('日本語'));
