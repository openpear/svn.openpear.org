<?php
include_once dirname(__FILE__) . '/t/t.php';

$lime = new lime_test;
$eol = HatenaSyntax_EndOfLine::getInstance();

$lime->is("\r\n", $eol->parse(context("\r\n")));
$lime->is('', $eol->parse(context('')));