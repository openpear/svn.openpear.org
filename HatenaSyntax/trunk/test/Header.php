<?php
include_once dirname(__FILE__) . '/t/t.php';

function hh($level, $body)
{
    return array('type' => 'header', 'level' => $level, 'body' => $body);
}

$lime = new lime_test;
$parser = new HatenaSyntax_Header;
$lime->is($parser->parse(context("*a\r\n")), 
          hh(1, array('a')));
$lime->is($parser->parse(context('***aa')), 
          hh(3, array('aa')));
$lime->is($parser->parse(context("***aa\n")), 
          hh(3, array('aa')));
$lime->is($parser->parse(context('***a((a))aa')), 
          hh(3, array('a', array('type' => 'footnote', 'body' => 'a'), 'aa')));