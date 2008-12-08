<?php
require_once('%s');

class Maple4_DocTest_Test1Test extends PHPUnit_Framework_TestCase
{
    private $obj;

    public function setUp()
    {
        $this->obj = new Test1();
    }

    public function tearDown()
    {
        $this->obj = null;
    }

    private $a;
    private $b;

    private function init()
    {
        $this->a = 10;
        $this->b = 5;
    }

    public function testMul()
    {
        $this->init();
        $this->assertEquals(50, $this->obj->mul($this->a, $this->b));
    }

    public function testMul2()
    {
        $this->assertEquals(48, $this->obj->mul(8, 6));
    }

    public function testAdd_add()
    {
        // add
        $this->init();
        $this->assertEquals(15, $this->obj->add($this->a, $this->b));

        $this->assertEquals(10, $this->obj->add(8, 2));
    }

    public function testSub()
    {
        // sub
        $this->init();
        $this->assertEquals(5, $this->obj->sub($this->a, $this->b));

        $this->assertEquals(6, $this->obj->sub(8, 2));
    }

    public function testSub_sub2()
    {
        // sub2
        $this->assertEquals(5, $this->obj->sub(8, 3));

        $this->assertEquals(4, $this->obj->sub(8, 4));
    }

    public function testMul_sub2()
    {
        // sub2
        $this->assertEquals(40, $this->obj->mul(8, 5));
    }
}
