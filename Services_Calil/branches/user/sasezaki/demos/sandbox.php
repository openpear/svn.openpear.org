<?php
use openpear\services\calil\Calil;
use openpear\services\calil\Query;


$calil = new Calil();
$query = Query::factory('check', array('appkey' => 'test',
                                       'isbn' => '1111111',
                                       'systemid' => 'test_pref'));

do {
    $result  = $calil->send($query);

    // do something

    $query->next();
} while ($query->valid());
