<?php
include_once dirname(__FILE__) . '/t/t.php';

$lime = new lime_test;
$p = HatenaSyntax_Table::getInstance();

$c = context('|hoge|
|hoge|');
$lime->is($p->parse($c),
          array('type' => 'table',
                'body' => array(array(array('header' => false,
                                            'body' => array('hoge'))),
                                array(array('header' => false,
                                            'body' => array('hoge'))))));
