<?php
include_once dirname(__FILE__) . '/../t.php';

$t = new lime_test;

$node = HatenaSyntax::parse('*hoge*header');
$t->is(HatenaSyntax::getSectionName($node), 'hoge');

$node = HatenaSyntax::parse('*hoge');
$t->is(HatenaSyntax::getSectionName($node), '');

