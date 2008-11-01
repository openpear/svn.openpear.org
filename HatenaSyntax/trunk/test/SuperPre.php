<?php
include_once dirname(__FILE__) . '/t/t.php';

$lime = new lime_test;

$p = HatenaSyntax_SuperPre::getInstance();

$c = context('>|hoge|
hh
oo
gg
ee||<');
$lime->is($p->parse($c),
          array('type' => 'superpre',
                'ext' => 'hoge',
                'body' => array('hh', 'oo', 'gg', 'ee')));

$c = context('>|hoge|
hh
oo
gg
||<');
$lime->is($p->parse($c),
          array('type' => 'superpre',
                'ext' => 'hoge',
                'body' => array('hh', 'oo', 'gg')));