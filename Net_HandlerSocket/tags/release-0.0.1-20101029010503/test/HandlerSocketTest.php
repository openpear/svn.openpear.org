<?php
require_once 'PHPUnit/Framework/TestCase.php';
require_once 'Net/HandlerSocket.php';
class HandlerSocketTest extends PHPUnit_Framework_TestCase{
    public function setUp(){
        $f = dirname(__FILE__)."/preparedb.sh";
        `sh $f`;
    }
    public function testFind(){
        $hs = new HandlerSocket('sag14', '9998');
        //primary key=1
        $hs->openIndex(1, 'hstest', 'table1', 'PRIMARY', array('id', 'enable', 'body'));
        $r = $hs->executeSingle(1, '=', array(1));
        $this->assertType('array', $r, '返り値は配列');
        $this->assertEquals(5, count($r), '5つ返る');
        $this->assertEquals(0, $r[0], '正常なレスポンスは1つめが0');
        $this->assertEquals('hoge', $r[4], 'レコードの中身が正常');
        //enable = 1; 2レコードヒットを期待
        $hs->openIndex(1, 'hstest', 'table1', 'idx_enable', array('id', 'enable', 'body'));
        $r = $hs->executeSingle(1, '=', array(1), 10);
        $this->assertEquals(0, $r[0], '正常なレスポンスは1つめが0');
        $this->assertEquals(8, count($r), 'レスポンス2 レコード6で配列の長さ8');
        //id=10; 0レコードヒットを期待
        $hs->openIndex(1, 'hstest', 'table1', 'PRIMARY', array('id', 'enable', 'body'));
        $r = $hs->executeSingle(1, '=', array(10), 10);
        $this->assertEquals(0, $r[0], '正常なレスポンスは1つめが0');
        $this->assertEquals(2, count($r), 'レコードがヒットしない場合長さ2');
    }
    public function testUpdate(){
        $hs = new HandlerSocket('sag14', '9999');
        //primary key=1
        $hs->openIndex(1, 'hstest', 'table1', 'PRIMARY', array('enable'));
        $r = $hs->executeSingle(1, '=', array(2), 1, 0);
        $this->assertEquals(0, $r[0], '正常なレスポンスは1つめが0');
        $this->assertEquals(0, $r[2], '更新前は0');
        $r = $hs->executeSingle(1, '=', array(2), 1, 0, 'U', array(1));
        $this->assertEquals(0, $r[0], '正常なレスポンス');
        $this->assertEquals(1, $r[1], '正常なレスポンスは2つが1');
        $this->assertEquals(1, $r[2], '1レコード修正');
        $r = $hs->executeSingle(1, '=', array(2), 1, 0);
        $this->assertEquals(0, $r[0], '正常なレスポンスは1つめが0');
        $this->assertEquals(1, $r[2], '更新された');
    }
    public function testInsert(){
        $hs = new HandlerSocket('sag14', '9999');
        //primary key=1
        $hs->openIndex(1, 'hstest', 'table1', 'PRIMARY', array('id', 'enable', 'body'));
        $r = $hs->executeSingle(1, '+', array(4, 0, 'hogera'));
        $this->assertEquals(0, $r[0], '正常なレスポンスは1つめが0');
        $this->assertEquals(1, $r[1], 'レコードが作成された場合1');
        $r = $hs->executeSingle(1, '=', array(4), 4, 0);
        $this->assertEquals(5, count($r), '1レコードが存在する');
        $this->assertEquals('hogera', $r[4], '1レコードが存在する');
        $this->assertEquals(4, $r[2], '1レコードが存在する');
    }
    public function testDelete(){
        $hs = new HandlerSocket('sag14', '9999');
        //primary key=1
        $hs->openIndex(1, 'hstest', 'table1', 'PRIMARY', array('id', 'enable', 'body'));
        $r = $hs->executeSingle(1, '=', array(1), 1, 0, 'D');
        $this->assertEquals(0, $r[0], '正常なレスポンスは1つめが0');
        $this->assertEquals(1, $r[1], '2つめが1');
        $r = $hs->executeSingle(1, '=', array(1), 1, 0);
        $this->assertEquals(2, count($r), '1レコードが削除された');
    }
    public function testExecuteMulti(){
        $hs = new HandlerSocket('sag14', '9998');
        $hs->openIndex(1, 'hstest', 'table1', 'PRIMARY', array('id', 'enable', 'body'));
        $r = $hs->executeMulti(array(array(1, '=', array(1)),
                                     array(1, '=', array(2)),
                                     ));
        $this->assertType('array', $r, '返り値が配列');
        $this->assertEquals(2, count($r), '3レスポンスが返る');
        foreach ($r as $res) {
            $this->assertEquals(5, count($res), 'レスポンス2 レコード3で長さ5');
            $this->assertEquals(0, $res[0], '正常なレスポンスは1つめが0');
        }
    }
}