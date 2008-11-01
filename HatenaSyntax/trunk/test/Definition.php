<?php
include_once dirname(__FILE__) . '/t/t.php';

$lime = new lime_test();

$parser = new HatenaSyntax_Definition;
$context = context(":hoge:fuga\r\n");
$lime->is($parser->parse($context), 
          array(array('hoge'), array('fuga')));
