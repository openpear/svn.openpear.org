<?php
include_once dirname(__FILE__) . '/../../t.php';

$t = new lime_test;

$p = new HatenaSyntax_Quote(PEG::anything());
$c = PEG::context(array(
    '>>',
    'a',
    '<<'
));

$t->is(
    $p->parse($c),
    array(
        false,
        array('a')
    ));

$c = PEG::context(array(
    '>http://google.com>',
    'a',
    '<<'
));

$t->is(
    $p->parse($c),
    array(
        'http://google.com',
        array('a')
    ));
