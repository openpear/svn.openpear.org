<?php
include_once dirname(__FILE__) . '/t/t.php';

$lime = new lime_test;
$p = new HatenaSyntax_List;

$c = context('+hoge');
$lime->is($p->parse($c),
          array('type' => 'list',
                'head' => '+',
                'body' => array(array('hoge'))));
          
$c = context('-hoge
+hoge');
$lime->is($p->parse($c),
          array('type' => 'list',
                'head' => '-',
                'body' => array(array('hoge'), array('hoge'))));

$c = context('-hoge
++hoge');
$lime->is($p->parse($c),
          array('type' => 'list',
                'head' => '-',
                'body' => array(array('hoge'),
                                array('type' => 'list',
                                      'head' => '+',
                                      'body' => array(array('hoge'))))));