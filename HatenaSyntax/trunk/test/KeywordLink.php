<?php
include_once dirname(__FILE__) . '/t/t.php';

$lime = new lime_test;
$parser = HatenaSyntax_Locator::it()->bracket;

//--

$context = PEG::context('[[keyword]]');
$result = $parser->parse($context);
$lime->is($result->getType(), 'keywordlink');
$lime->is($result->getData(), 'keyword');

//--

$context = PEG::context('[[javascript:alert(\'hahaha\')]]');
$result = $parser->parse($context);
$lime->is($result, PEG::failure());

//--

$context = PEG::context('[[   javascript:alert(\'hahaha\')]]');
$result = $parser->parse($context);
$lime->is($result, PEG::failure());