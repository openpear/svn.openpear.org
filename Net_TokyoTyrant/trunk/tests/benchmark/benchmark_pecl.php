<?php
//require_once dirname(dirname(__FILE__)) . '/Net/TokyoTyrant.php';
require_once 'Benchmark/Timer.php';
$timer = new Benchmark_Timer();
$timer->start();

$tt = new TokyoTyrant();
$timer->setMarker('create');
$tt->connect('localhost', 1978);
$timer->setMarker('connect');


$data = 'aaaaaaa';
for($i = 0;$i <= 10000 ;$i++){
   $key = (string) rand(1,100);
   $tt->put($key, $data);
}

$timer->setMarker('put');

for($i = 0;$i <= 10000 ;$i++){
   $key = (string) rand(1,100);
  $data = $tt->get($key);
}

$timer->setMarker('get');

$timer->setMarker('close');
$timer->display();
