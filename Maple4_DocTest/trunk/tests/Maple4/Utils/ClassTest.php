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
 * @category   Utils
 * @package    Maple4_Utils
 * @copyright  2008 Maple Project
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @since      File available since Release 0.2.0
 */

require_once('PHPUnit/Framework.php');
require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/src/Maple4/Utils/Class.php');

/**
 * クラス関連のユーティリティークラスのテスト
 *
 * @category   Utils
 * @package    Maple4_Utils
 * @author     TAKAHASHI Kunihiko <kunit@maple-project.com>
 * @copyright  2008 Maple Project
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @since      Class available since Release 0.2.0
 */
class Maple4_Utils_ClassTest extends PHPUnit_Framework_TestCase
{
    private $instance;

    /**
     * テストの下準備
     */
    public function setUp()
    {
        $this->instance = new Maple4_Utils_Class();
    }

    /**
     * テストの後始末
     */
    public function tearDown()
    {
        $this->instance = null;
    }

    /**
     * パス名からクラス名を正しく生成できるか？
     */
    public function testToClassname()
    {
        $pathname = 'Test1.txt';
        $expected = null;
        $this->assertEquals($expected, $this->instance->toClassName($pathname));

        $pathname = 'Test1.php';
        $expected = 'Test1';
        $this->assertEquals($expected, $this->instance->toClassName($pathname));

        $pathname = 'Test1/Test2.php';
        $expected = 'Test1_Test2';
        $this->assertEquals($expected, $this->instance->toClassName($pathname));

        $pathname = 'Test1/Test2.php';
        $expected = 'test1_test2';
        $options = array('ucfirst' => false);
        $this->assertEquals($expected, $this->instance->toClassName($pathname, $options));

        $pathname = 'Test2.php';
        $expected = 'Test1_Test2';
        $options = array('namespace' => 'Test1');
        $this->assertEquals($expected, $this->instance->toClassName($pathname, $options));

        $pathname = 'Test3.php';
        $expected = 'Test1_Test2_Test3';
        $options = array('namespace' => 'Test1_Test2');
        $this->assertEquals($expected, $this->instance->toClassName($pathname, $options));
    }
}