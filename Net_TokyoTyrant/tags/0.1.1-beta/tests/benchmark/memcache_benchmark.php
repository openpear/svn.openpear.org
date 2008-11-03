<?php
require_once 'Benchmark/Timer.php';
$timer = new Benchmark_Timer();
$timer->start();

$memcache = new memcache();
$timer->setMarker('create');
$memcache->addServer('localhost');
$timer->setMarker('connect');

$data = 'aaaaaaa';
for($i = 0;$i <= 10000 ;$i++){
   $key = (string) rand(1,100);
   $memcache->set($key, $data);
}

$timer->setMarker('put');

for($i = 0;$i <= 10000 ;$i++){
   $key = (string) rand(1,100);
   $data = $memcache->get($key);
}

$timer->setMarker('get');

$memcache->close();
$timer->setMarker('close');
$timer->display();
