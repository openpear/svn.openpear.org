<?php
include_once dirname(__FILE__) . '/t/t.php';

$lime = new lime_test;
$link = HatenaSyntax_Locator::it()->link;

//--

$context = PEG::context('[http://google.com:title=hoge]');
$lime->is($link->parse($context)->getData(), 
          array('href' => 'http://google.com', 'title' => 'hoge'));
