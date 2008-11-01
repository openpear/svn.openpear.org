<?php
include_once dirname(__FILE__) . '/t/t.php';

$lime = new lime_test;
$parser = HatenaSyntax_HttpLink::getInstance();

$lime->is($parser->parse(context('[http://hgoehoge]')),
          array('type' => 'link', 'title' => null, 'body' => 'http://hgoehoge'));
          
$lime->is($parser->parse(context('[http://hgoehoge:title=hogehoge]')),
          array('type' => 'link', 'title' => 'hogehoge', 'body' => 'http://hgoehoge'));
          
$lime->is($parser->parse(context('[https://hgoehoge]')),
          array('type' => 'link', 'title' => null, 'body' => 'https://hgoehoge'));