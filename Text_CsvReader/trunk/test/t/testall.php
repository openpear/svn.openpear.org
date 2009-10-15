<?php
include_once(dirname(__FILE__) . '/t.php');

$h = new lime_harness(new lime_output_color());
$h->register_glob(dirname(__FILE__) . '/../*.php');
$h->run();

if (extension_loaded('xdebug')) {
  $c = new lime_coverage($h);
  $c->base_dir = realpath(dirname(__FILE__).'/../../src/');
  $c->register($c->base_dir.'/Text/CsvReader.php');
  $c->register_glob($c->base_dir.'/Text/CsvReader/*.php');
  $c->register_glob($c->base_dir.'/Text/CsvReader/Reader/*.php');
  $c->register_glob($c->base_dir.'/Text/CsvReader/Filter/*.php');
  $c->register_glob($c->base_dir.'/Text/CsvReader/Grepper/*.php');
  $c->register_glob($c->base_dir.'/Text/CsvReader/Validator/*.php');
  $c->register_glob($c->base_dir.'/Text/CsvReader/Writer/*.php');
  $c->run();
}