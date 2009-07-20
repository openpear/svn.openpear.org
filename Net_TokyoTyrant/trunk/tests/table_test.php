<?php
ini_set('memory_limit', -1);
ini_set('display_errors', 'On');
error_reporting(E_ALL);
require_once dirname(dirname(__FILE__)) . '/Net/TokyoTyrant/Table.php';

$test_data = array();
$test_data[] = array('name' => 'Alex Low', 'vehicle' => 'silvana', 'age' => 29);
$test_data[] = array('name' => 'Claus Valca', 'vehicle' => 'vanship', 'age' => 15);
$test_data[] = array('name' => 'Lavie Head', 'vehicle' => 'vanship', 'age' => 15);

$ttt = new Net_TokyoTyrant_Table();
$ttt->connect('localhost', 11978, 1000);
assert($ttt->vanish() === true);
assert($ttt->setindex('name',  Net_TokyoTyrant_Table::ITLEXICAL));

assert(is_string($uid1 = $ttt->genuid()));
assert($ttt->put($uid1 , $test_data[0]));
assert(count(array_diff($ttt->get($uid1),$test_data[0])) === 0);

assert($ttt->out($uid1));
assert($ttt->get($uid1) === false);
assert($ttt->put($uid1 , $test_data[0]));


assert($ttt->putkeep($uid1 , $test_data[0]) == false);
assert(is_string($uid2 = $ttt->genuid()));
assert($ttt->putkeep($uid2 , $test_data[0]));
assert(count(array_diff($ttt->get($uid2),$test_data[0])) === 0);

assert($ttt->putcat($uid2 , $test_data[0]));
assert(count(array_diff($ttt->get($uid2),$test_data[0])) === 0);

assert(is_array($ttt->mget(array($uid1, $uid2))));

//clear
assert($ttt->vanish() === true);
assert($ttt->setindex('name',  Net_TokyoTyrant_Table::ITLEXICAL));
assert(is_string($uid1 = $ttt->genuid()));
assert($ttt->put($uid1 , $test_data[0]));

assert(is_string($uid2 = $ttt->genuid()));
assert($ttt->put($uid2 , $test_data[1]));
assert(is_string($uid3 = 'aaaaa'));
assert($ttt->put($uid3 , $test_data[1]));

//Query
$ttq = $ttt->getQuery();
$ttq->addcond("vehicle", Net_TokyoTyrant_Query::QCSTRINC, "silvana");
$ttq->setorder("age", Net_TokyoTyrant_Query::QCSTRINC);
$ttq->setlimit(10);

assert($ttq->searchcount() === 1);
$result = $ttq->search();
assert($result[0] === $uid1);

$result = $ttq->searchget();
assert(count(array_diff($result[0]['value'], $test_data[0])) === 0);

$ttq = $ttt->getQuery();
$ttq->setorder("age", Net_TokyoTyrant_Query::QONUMASC);
$ttq->setlimit(1);
$result = $ttq->searchget();
assert((int)$result[0]['value']['age'] === $test_data['1']['age']);
assert($ttq->searchcount() === 1);


$ttq = $ttt->getQuery();
$ttq->addcond("vehicle", Net_TokyoTyrant_Query::QCSTRINC, "vanship");
$ttq->setorder("age", Net_TokyoTyrant_Query::QCSTRINC);
assert($ttq->searchcount() === 2);

$ttt->close();
