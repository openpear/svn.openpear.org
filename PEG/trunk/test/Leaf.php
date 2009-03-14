<?php
include_once dirname(__FILE__) . '/t/t.php';
$lime = new lime_test;

//--

$parser = PEG::leaf(1);
$context = PEG::context(array(1));
$lime->is($parser->parse($context), array(1));

//--

$parser = PEG::seq(PEG::flatten(PEG::many1(PEG::leaf(1))), PEG::leaf(2, 3));
$context = PEG::context(array(1, 1, 1, 2, 3));
$lime->is($parser->parse($context),
          array(array(1, 1, 1), array(2, 3)));