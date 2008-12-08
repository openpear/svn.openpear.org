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
require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/src/Maple4/Utils/Array.php');

/**
 * 配列関連のユーティリティークラスのテスト
 *
 * @category   Utils
 * @package    Maple4_Utils
 * @author     TAKAHASHI Kunihiko <kunit@maple-project.com>
 * @copyright  2008 Maple Project
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @since      Class available since Release 0.2.0
 */
class Maple4_Utils_ArrayTest extends PHPUnit_Framework_TestCase
{
    /**
     * 配列から安全に値を取得できるか？
     */
    public function testGetValue()
    {
        $test = array(
            'foo' => 1,
            'bar' => 2,
        );

        $instance = new Maple4_Utils_Array($test);

        $this->assertEquals(1, $instance->get('foo'));
        $this->assertEquals(2, $instance->get('bar'));
        $this->assertEquals(null, $instance->get('baz'));

        $this->assertEquals(1, $instance->foo);
        $this->assertEquals(2, $instance->bar);
        $this->assertEquals(null, $instance->baz);

        $this->assertEquals(1, Maple4_Utils_Array::create($test)->foo);
        $this->assertEquals(2, Maple4_Utils_Array::create($test)->bar);
        $this->assertEquals(null, Maple4_Utils_Array::create($test)->baz);
    }

    /**
     * デフォルト値を利用できるか？
     */
    public function testGetDefaultValue()
    {
        $test = array(
            'foo' => 1,
            'bar' => 2,
        );

        $instance = new Maple4_Utils_Array($test);

        $this->assertEquals(3, $instance->get('baz', 3));
        $this->assertEquals(3, $instance->setDefault('baz', 3)->baz);
        $this->assertEquals(3, Maple4_Utils_Array::create($test)->setDefault('baz', 3)->baz);

        $this->assertEquals(3, Maple4_Utils_Array::create()->setDefault('baz', 3)->get('baz'));
        $this->assertEquals(3, Maple4_Utils_Array::create()->setDefault('baz', 3)->baz);
    }

    /**
     * 値をセットして再取得できるか？
     */
    public function testSetValue()
    {
        $test = array(
            'foo' => 1,
            'bar' => 2,
        );

        $instance = new Maple4_Utils_Array($test);

        $this->assertEquals(1, $instance->get('foo'));
        $this->assertEquals(2, $instance->set('foo', 2)->get('foo'));

        $instance->bar = 3;
        $this->assertEquals(3, $instance->get('bar'));
        $this->assertEquals(3, $instance->bar);

        $this->assertEquals(2, Maple4_Utils_Array::create($test)->set('foo', 2)->get('foo'));
        $this->assertEquals(2, Maple4_Utils_Array::create($test)->set('foo', 2)->foo);

        $this->assertEquals(3, Maple4_Utils_Array::create()->set('bar', 3)->get('bar'));
        $this->assertEquals(3, Maple4_Utils_Array::create()->set('bar', 3)->bar);
    }
}