<?php
include_once dirname(__FILE__) . '/t/t.php';

$lime = new lime_test;
$p = new HatenaSyntax_Pre;

$c = context('>|
hoge
hoge|<');
$lime->is($p->parse($c), 
          array('type' => 'pre',
                'body' => array(array('hoge'), array('hoge'))));

$c = context('>|
hoge
hoge
|<');
$lime->is($p->parse($c), 
          array('type' => 'pre',
                'body' => array(array('hoge'), array('hoge'))));

$c = context('>|
hoge
hoge((hoge))
|<');
$lime->is($p->parse($c), 
          array('type' => 'pre',
                'body' => array(array('hoge'), 
                                array('hoge', 
                                      array('type' => 'footnote', 
                                            'body' => 'hoge')))));

$c = context('>|
|<');
$lime->is($p->parse($c), 
          array('type' => 'pre',
                'body' => array()));