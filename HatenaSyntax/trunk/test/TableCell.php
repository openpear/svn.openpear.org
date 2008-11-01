<?php
include_once dirname(__FILE__) . '/t/t.php';

$lime = new lime_test;
$p = new HatenaSyntax_TableCell();

$c = context("|*hogehoge|hoge((aa))|fuga\n");
$lime->is($p->parse($c),
          array('header' => true,
                'body' => array('hogehoge')));
$lime->is($p->parse($c),
          array('header' => false,
                'body' => array('hoge',
                                array('type' => 'footnote',
                                      'body' => 'aa'))));
$lime->is($p->parse($c),
          array('header' => false,
                'body' => array('fuga')));
