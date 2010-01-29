<?php
include_once dirname(__FILE__) . '/../t.php';

$t = new lime_test;

$node = HatenaSyntax::parse('*hoge*header');
$t->is(HatenaSyntax::getSectionName($node), 'hoge');

$node = HatenaSyntax::parse('*hoge');
$t->is(HatenaSyntax::getSectionName($node), '');

$node = HatenaSyntax::parse('**hoge*header');
$t->is(HatenaSyntax::getSectionName($node), '');

$nodes = HatenaSyntax::parseAsSections("\n*hoge\n*fuga\n*piyo");
$t->is(count($nodes), 3);

$nodes = HatenaSyntax::parseAsSections("*hoge\n\nfuga");
$t->is(count($nodes), 1);

$title = HatenaSyntax::getSectionTitle(HatenaSyntax::parse('*hahaha'));
$t->is($title, 'hahaha');

$title = HatenaSyntax::getSectionTitle(HatenaSyntax::parse('*hoge*s[http://google.com]'));
$t->is($title, 'shttp://google.com');

$hasTopHeader = HatenaSyntax::hasTopHeader(HatenaSyntax::parse('*hoge'));
$t->ok($hasTopHeader);

$hasTopHeader = HatenaSyntax::hasTopHeader(HatenaSyntax::parse('hoge'));
$t->ok(!$hasTopHeader);
