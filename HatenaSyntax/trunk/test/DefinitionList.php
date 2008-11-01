<?php
include_once dirname(__FILE__) . '/t/t.php';

$lime = new lime_test;
$parser = new HatenaSyntax_DefinitionList;

$context = context(":a:b\r\n:b:c");
$lime->is($parser->parse($context),
          array('type' => 'definitionlist',
                'body' => array(array(array('a'),
                                      array('b')),
                                array(array('b'),
                                      array('c')))));