<?php
include_once dirname(__FILE__) . '/t/t.php';

$lime = new lime_test;

$parser = PEG::not(PEG::token('hoge'));

$lime->is($parser->parse(PEG::context('fuga')), 'fuga');
$lime->is($parser->parse(PEG::context('hoge')), PEG::failure());