<?php
include_once dirname(__FILE__) . '/t.php';

$h = new lime_harness(new lime_output_color());
$h->register_glob(dirname(__FILE__) . '/../*.php');
$h->run();

$c = new lime_coverage($h);
$c->base_dir = realpath(dirname(__FILE__).'/../../src/');
$c->register_glob($c->base_dir.'/*.php');
$c->run();