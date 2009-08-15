<?php
/**
 * Db_Fixture_Tests

 * PHP version 5.2
 *
 * Copyright (c) 2009 Heavens hell, All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Heavens hell nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category  Db
 * @package   Db_Fixture
 * @version   $id$
 * @copyright 2009 Heavens hell
 * @author    Heavens hell <heavenshell.jp@gmail.com>
 * @license   New BSD License
 */
$rootPath = dirname(dirname(__FILE__));
$libPath  = $rootPath . DIRECTORY_SEPARATOR . 'src';

set_include_path($libPath . PATH_SEPARATOR . get_include_path());

require_once 'lime.php';
require_once 'Db/Fixture.php';

$t    = new lime_test(null, new lime_output_color());
$obj  = Db_Fixture::load(dirname(__FILE__) . '/testdata/test.json');
$pdo  = $obj->getConnection();
$file = file_get_contents(dirname(__FILE__) . '/testdata/test.sql');
$stmt = $pdo->prepare($file);
$ret  = $stmt->execute();
$stmt = null;

// Create table
$t->ok($ret === true, 'Success to create table');
$t->ok($obj instanceof Db_Fixture, 'Db_Fixture::load should return Db_Fixture object');
$t->ok($pdo instanceof Pdo , 'Db_Fixture::getConnection should return Pdo object');

$f = $obj->insert()->fixtures();

// test1.row1
$t->ok($f['test1']['auto_increment'] === 'id', 'test1 auto increment should be id');
$t->ok($f['test1']['row1']['test_id'] === '1', 'test_id should be 1');
$t->ok($f['test1']['row1']['test1'] === 'test', 'test1 should be test');
$t->ok($f['test1']['row1']['created_at'] === '2009-03-20 20:00:00', 'created_at should be 2009-03-20 20:00:00');

// test1.row2
$t->ok($f['test1']['row2']['test_id'] === '2', 'test_id should be 2');
$t->ok($f['test1']['row2']['test1'] === 'test test', 'test1 should be test test');
$t->ok($f['test1']['row2']['created_at'] === '2009-03-20 20:00:10', 'created_at should be 2009-03-20 20:00:10');

// test1.row3
$t->ok($f['test1']['row3']['test_id'] === '3', 'test_id should be 3');
$t->ok($f['test1']['row3']['test1'] === 'test test test', 'test1 should be test test test');
$t->ok($f['test1']['row3']['created_at'] === '2009-03-20 20:00:20', 'created_at should be 2009-03-20 20:00:20');

// test2.row2
$t->ok($f['test2']['row1']['test_id'] === '1', 'test_id should be 1');
$t->ok($f['test2']['row1']['test2'] === '2', 'test2 should be 2');
$t->ok($f['test2']['row1']['created_at'] === '2009-03-20 20:00:30', 'created_at should be 2009-03-20 20:00:30');

// test1.row3
$t->ok($f['test2']['row2']['test_id'] === '1', 'test_id should be 1');
$t->ok($f['test2']['row2']['test2'] === '3', 'test2 should be 3');
$t->ok($f['test2']['row2']['created_at'] === '2009-03-20 20:00:40', 'created_at should be 2009-03-20 20:00:40');

$stmt = $pdo->prepare('SELECT * FROM test1');
$ret  = $stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get inserted data from database
$t->ok(count($rows) === count($f['test1']) - 1, 'Inserted test1 table count should be 3');
$t->ok(count($rows[0]) === count($f['test1']), 'test1 row count should be 4');
$id = $obj->getLastInsertedId();
$t->ok($rows[0]['id'] === $id['test1']['id'][0], 'test1 row1 id should equal last inserted id');

// row1
$t->ok($rows[0]['test_id'] === $f['test1']['row1']['test_id'], 'test1 row1 test_id should equal test');
$t->ok($rows[0]['created_at'] === $f['test1']['row1']['created_at'], 'test1 row1 created_at should equal 2009-03-20 20:00:00');

// row2
$t->ok($rows[1]['id'] === $id['test1']['id'][1], 'test1 row2 id should equal last inserted id');
$t->ok($rows[1]['test_id'] === $f['test1']['row2']['test_id'], 'test1 row2 test_id should equal test test');
$t->ok($rows[1]['created_at'] === $f['test1']['row2']['created_at'], 'test1 row2 created_at should equal 2009-03-20 20:00:10');

// row3
$t->ok($rows[2]['id'] === $id['test1']['id'][2], 'test1 row3 id should equal last inserted id');
$t->ok($rows[2]['test_id'] === $f['test1']['row3']['test_id'], 'test1 row3 test_id should equal test test test');
$t->ok($rows[2]['created_at'] === $f['test1']['row3']['created_at'], 'test1 row3 created_at should equal 2009-03-20 20:00:20');

$stmt = $pdo->prepare('SELECT * FROM test2');
$ret  = $stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt = null;

// table2 row count
$t->ok(count($rows) === count($f['test2']) - 1, 'Inserted test2 table count should be 3');
$t->ok(count($rows[0]) === count($f['test2']), 'test2 row count should be 4');

// table2.row1
$t->ok($rows[0]['test_id'] === $f['test2']['row1']['test_id'], 'test2 row1 test_id should equal 1');
$t->ok($rows[0]['test2'] === $f['test2']['row1']['test2'], 'test2 row1 test_id should equal 2');
$t->ok($rows[0]['created_at'] === $f['test2']['row1']['created_at'], 'test2 row1 created_at should equal 2009-03-20 20:00:30');

// table2.row2
$t->ok($rows[1]['test_id'] === $f['test2']['row2']['test_id'], 'test2 row2 test_id should equal 2');
$t->ok($rows[1]['test2'] === $f['test2']['row2']['test2'], 'test2 row2 test_id should equal 2');
$t->ok($rows[1]['created_at'] === $f['test2']['row2']['created_at'], 'test2 row2 created_at should equal 2009-03-20 20:00:40');

// primary key
$t->ok($f['test2']['primary_key'][0] === 'test_id', 'primary key should be test_id');
$t->ok($f['test2']['primary_key'][1] === 'test2', 'primary key should be test2');

// Delete data
$obj->delete();
$stmt = $pdo->prepare('SELECT * FROM test1');
$ret  = $stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt = null;

$t->ok(count($rows) === 0, 'table1 data is deleted');

$stmt = $pdo->prepare('SELECT * FROM test2');
$ret  = $stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt = null;

$t->ok(count($rows) === 0, 'table2 data is deleted');

// Drop table
$stmt = $pdo->prepare('DROP TABLE IF EXISTS `test1`;DROP TABLE IF EXISTS `test2`;');
$stmt->execute();
$stmt = null;

// Delete object
$obj->after();
$t->ok($obj->getConnection() === null, 'Pdo object should be null');
$t->ok($obj->fixtures() === null, 'fixture should be null');
$t->ok($obj->getLastInsertedId() === null, 'last inserted id should be null');

// Exception
try {
    $t->fail(Db_Fixture::load('/dummy.json'));
} catch (Db_Fixture_Exception $e) {
    $t->ok($e->getMessage() === '/dummy.json not found.', 'exception should be occured when fixture file not found');
}

try {
    $t->fail(Db_Fixture::load(dirname(__FILE__) . '/testdata/test.json', 'dummyconfig.json'));
} catch (Db_Fixture_Exception $e) {
    $t->ok($e->getMessage() === 'dummyconfig.json not found.', 'exception should be occured when config file not found');
}

try {
    $t->fail(Db_Fixture::insert());
} catch (Db_Fixture_Exception $e) {
    $t->ok($e->getMessage() === 'Fixture does not set.', 'exception should be occured when fixture not set');
}

// Exec sql
$path = dirname(__FILE__);
$obj = Db_Fixture::execute($path . '/testdata/insert.sql', $path . '/config/database.json');

$pdo  = $obj->getConnection();
$stmt = $pdo->prepare('SELECT * FROM test1');
$ret  = $stmt->execute();
$rows = $stmt->fetchAll();
$t->ok(count($rows) === 2, 'Inserted row count should be 2');
$stmt = null;
$pdo  = null;
$obj->after();

