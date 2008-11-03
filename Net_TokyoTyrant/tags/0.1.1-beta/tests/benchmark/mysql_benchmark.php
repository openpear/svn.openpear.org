<?php
require_once 'Benchmark/Timer.php';
$timer = new Benchmark_Timer();
$timer->start();

$timer->setMarker('create');
$dbh = new PDO('mysql:host=localhost;dbname=benchmark', 'root');
$timer->setMarker('connect');

$stmt = $dbh->prepare("INSERT INTO bench (name, value) VALUES (:name, :value)");
$value = 'aaaaaa';
for($i = 0;$i <= 10000 ;$i++){
    $name = rand(1, 1000);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':value', $value);
    $stmt->execute();
}

$timer->setMarker('put');
$stmt = $dbh->prepare("SELECT * FROM bench where name = ?");
for($i = 0;$i <= 10000 ;$i++){
    $name = (string) rand(1, 1000);
    $stmt->execute(array($name));
    $row = $stmt->fetch();
}

$timer->setMarker('get');

$dbh = null;
$timer->setMarker('close');
$timer->display();
