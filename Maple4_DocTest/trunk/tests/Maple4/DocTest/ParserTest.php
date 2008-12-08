<?php
/**
 * PHP versions 5
 *
 * Copyright (c) 2008 Maple Project, All rights reserved.
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
 *   * Neither the name of Sebastian Bergmann nor the names of his
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
 * @category   Testing
 * @package    Maple4_DocTest
 * @copyright  2008 Maple Project
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @since      File available since Release 0.2.0
 */

require_once('PHPUnit/Framework.php');
require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/src/Maple4/DocTest/Parser.php');

/**
 * PHPファイルからDocTestコメントを抜き出すクラスのテスト
 *
 * @category   Testing
 * @package    Maple4_DocTest
 * @author     TAKAHASHI Kunihiko <kunit@maple-project.com>
 * @copyright  2008 Maple Project
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @since      Class available since Release 0.2.0
 */
class Maple4_DocTest_ParserTest extends PHPUnit_Framework_TestCase
{
    private $instance;
    private $baseDir;

    /**
     * テストの下準備
     */
    public function setUp()
    {
        $this->instance = new Maple4_DocTest_Parser();
        $this->baseDir = dirname(dirname(dirname(dirname(__FILE__))));
    }

    /**
     * テストの後始末
     */
    public function tearDown()
    {
        $this->instance = null;
        $this->baseDir = null;
    }

    /**
     * PHPファイルからDocTestコメントを正しく抜き出せるか？
     */
    public function testParse()
    {
        $pathname = $this->baseDir . '/fixtures/Maple4/DocTest/tests';

        $expected = array (
            '__noop' => array (
                'method' => '',
                'userdefine' => '__noop',
                'body' => 'private $obj;
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
    #eq(48, $this->obj->mul(8, 6));
}',
            ),
            '__setup' => array (
                'method' => '',
                'userdefine' => '__setUp',
                'body' => '$this->obj = new #class;',
            ),
            '__teardown' => array (
                'method' => '',
                'userdefine' => '__tearDown',
                'body' => '$this->obj = null;',
            ),
            'add_add' => array (
                'method' => 'add',
                'userdefine' => 'add',
                'body' => '$this->init();
$this->assertEquals(15, $this->obj->add($this->a, $this->b));

$this->assertEquals(10, $this->obj->add(8, 2));',
            ),
            'sub' => array (
                'method' => 'sub',
                'userdefine' => '',
                'body' => '$this->init();
#eq(5, $this->obj->sub($this->a, $this->b));

#eq(6, $this->obj->sub(8, 2));',
            ),
            'sub_sub2' => array (
                'method' => 'sub',
                'userdefine' => 'sub2',
                'body' => '#eq(5, $this->obj->sub(8, 3));

#eq(4, $this->obj->sub(8, 4));',
            ),
            'mul_sub2' => array (
                'method' => 'mul',
                'userdefine' => 'sub2',
                'body' => '#eq(40, $this->obj->mul(8, 5));',
            ),
        );

        $files = array(
             "{$pathname}/Test1.php" => 'Test1.php',
             "{$pathname}/Test1/Test4.php" => 'Test1/Test4.php',
             "{$pathname}/Test2.php" => 'Test2.php',
             "{$pathname}/Test2/Test5.php" => 'Test2/Test5.php',
             "{$pathname}/Test2/Test5/Test7.php" => 'Test2/Test5/Test7.php',
             "{$pathname}/Test3.php" => 'Test3.php',
             "{$pathname}/Test3/Test6.php" => 'Test3/Test6.php',
             "{$pathname}/Test3/Test6/Test8.php" => 'Test3/Test6/Test8.php',
             "{$pathname}/Test3/Test6/Test8/Test9.php" => 'Test3/Test6/Test8/Test9.php',
         );

        foreach ($files as $realpath => $path) {
            $this->assertEquals($expected, $this->instance->parse($realpath, $path));
        }
    }
}