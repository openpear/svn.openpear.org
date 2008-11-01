<?php
include_once dirname(__FILE__) . '/t/t.php';

$lime = new lime_test;

$parser = new HatenaSyntax_LineSegment(PEG_Token::get(':'));

$lime->is($parser->parse(context('hoge')), array('hoge'));
$lime->is($parser->parse(context('ho:')), array('ho'));
$lime->is($parser->parse(context('((hoge))ho')), 
          array(array('type' => 'footnote', 'body' => 'hoge'), 'ho'));