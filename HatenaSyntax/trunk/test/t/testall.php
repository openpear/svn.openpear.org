<?php
include_once 'lime.php';
include_relative('t.php');

$lime = new lime_harness(null);
$lime->register_glob(dirname(__FILE__) . '/../*.php');
$lime->run();


