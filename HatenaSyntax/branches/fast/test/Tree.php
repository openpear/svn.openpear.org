<?php
include_once dirname(__FILE__) . '/t/t.php';
$t = new lime_test;

//--

$arr = array(
    array('level' => 1, 'value' => 'a'),
    array('level' => 2, 'value' => 'b'),
    array('level' => 2, 'value' => 'c')
);

$root = HatenaSyntax_Tree::make($arr);
$t->is($root->hasChildren(), true);
$children = $root->getChildren();
$t->is($children[0]->getValue(), 'a');
$t->is($children[0]->hasChildren(), true);
$children = $children[0]->getChildren();
$t->is($children[0]->getValue(), 'b');
$t->is($children[1]->getValue(), 'c');

//--

$arr = array(
    array('level' => 2, 'value' => 'a'),
    array('level' => 1, 'value' => 'b')
);

$root = HatenaSyntax_Tree::make($arr);
$children = $root->getChildren();
$t->is($children[1]->getValue(), 'b');
$t->is($children[1]->hasChildren(), false);
$children = $children[0]->getChildren();
$t->is($children[0]->getValue(), 'a');