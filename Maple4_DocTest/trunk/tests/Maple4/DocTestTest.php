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
require_once(dirname(dirname(dirname(__FILE__))) . '/src/Maple4/DocTest.php');

/**
 * DocTest本体のテスト
 *
 * @category   Testing
 * @package    Maple4_DocTest
 * @author     TAKAHASHI Kunihiko <kunit@maple-project.com>
 * @copyright  2008 Maple Project
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @since      Class available since Release 0.2.0
 */
class Maple4_DocTestTest extends PHPUnit_Framework_TestCase
{
    private $baseDir;

    /**
     * テストの下準備
     */
    public function setUp()
    {
        $this->baseDir = dirname(dirname(dirname(__FILE__)));
    }

    /**
     * テストの後始末
     */
    public function tearDown()
    {
        $this->baseDir = null;
    }

    /**
     * 正しくディレクトリに対してテストが実行できるか？
     */
    public function testRunDir()
    {
        $pathname = $this->baseDir . '/fixtures/Maple4/DocTest/tests';

        $options = array(
            'compileDir' => $this->baseDir . '/tmp/tests_c',
            'color' => false,
            'ignore' => array('\.svn'),
            'report' => null,
            'forceCompile' => true,
        );

        ob_start();
        Maple4_DocTest::create()->run($pathname, $options);
        $actual = ob_get_contents();
        ob_end_clean();

        $this->assertEquals(1, preg_match('|OK \(54 tests\)|', $actual));

        ob_start();
        Maple4_DocTest::create()->run("{$pathname}/Test3", $options);
        $actual = ob_get_contents();
        ob_end_clean();

        $this->assertEquals(1, preg_match('|OK \(18 tests\)|', $actual));

        ob_start();
        Maple4_DocTest::create()->run("{$pathname}/Test3/Test6", $options);
        $actual = ob_get_contents();
        ob_end_clean();

        $this->assertEquals(1, preg_match('|OK \(12 tests\)|', $actual));

        ob_start();
        Maple4_DocTest::create()->run("{$pathname}/Test3/Test6/Test8", $options);
        $actual = ob_get_contents();
        ob_end_clean();

        $this->assertEquals(1, preg_match('|OK \(6 tests\)|', $actual));
    }

    /**
     * ファイルが存在しないディレクトリに対してテストが実行できるか？
     */
    public function testRunNofileDir()
    {
        $pathname = $this->baseDir . '/fixtures/Maple4/DocTest/nofiletests';

        $options = array(
            'compileDir' => $this->baseDir . '/tmp/tests_c',
            'color' => false,
            'ignore' => array('\.svn'),
            'report' => null,
            'forceCompile' => true,
        );

        ob_start();
        Maple4_DocTest::create()->run($pathname, $options);
        $actual = ob_get_contents();
        ob_end_clean();

        $expected = '';
        $this->assertEquals($expected, $actual);
    }

    /**
     * 正しくファイルに対してテストが実行できるか？
     */
    public function testRunFile()
    {
        $pathname = $this->baseDir . '/fixtures/Maple4/DocTest/tests';

        $options = array(
            'compileDir' => $this->baseDir . '/tmp/tests_c',
            'color' => true,
            'ignore' => array('\.svn'),
            'report' => null,
            'forceCompile' => true,
        );

        ob_start();
        Maple4_DocTest::create()->run("{$pathname}/Test1.php", $options);
        $actual = ob_get_contents();
        ob_end_clean();

        $this->assertEquals(1, preg_match('|OK \(6 tests\)|', $actual));

        ob_start();
        Maple4_DocTest::create()->run("{$pathname}/Test3/Test6/Test8/Test9.php", $options);
        $actual = ob_get_contents();
        ob_end_clean();

        $this->assertEquals(1, preg_match('|OK \(6 tests\)|', $actual));
    }

    /**
     * テストが記述されていないファイルに対してテストが実行できるか？
     */
    public function testRunNoTestFile()
    {
        $pathname = $this->baseDir . '/fixtures/Maple4/DocTest/notests';

        $options = array(
            'compileDir' => $this->baseDir . '/tmp/tests_c',
            'color' => true,
            'ignore' => array('\.svn'),
            'report' => null,
            'forceCompile' => true,
        );

        ob_start();
        Maple4_DocTest::create()->run($pathname, $options);
        $actual = ob_get_contents();
        ob_end_clean();

        $expected = '';
        $this->assertEquals($expected, $actual);

        ob_start();
        Maple4_DocTest::create()->run("{$pathname}/NoTest.php", $options);
        $actual = ob_get_contents();
        ob_end_clean();

        $expected = '';
        $this->assertEquals($expected, $actual);
    }

    /**
     * 変更した時だけテストが実行されるか？
     */
    public function testRunUpdatedFile()
    {
        $pathname = $this->baseDir . '/fixtures/Maple4/DocTest/tests';

        $options = array(
            'compileDir' => $this->baseDir . '/tmp/tests_c',
            'color' => true,
            'ignore' => array('\.svn'),
            'report' => null,
            'forceCompile' => false,
            'forceRun' => false,
        );

        // 更新がなければ実行されない
        ob_start();
        Maple4_DocTest::create()->run("{$pathname}/Test1.php", $options);
        $actual = ob_get_contents();
        ob_end_clean();

        $expected = '';
        $this->assertEquals($expected, $actual);

        // 更新されたので実行される
        clearstatcache();
        touch("{$pathname}/Test1.php");

        ob_start();
        Maple4_DocTest::create()->run("{$pathname}/Test1.php", $options);
        $actual = ob_get_contents();
        ob_end_clean();
        $this->assertEquals(1, preg_match('|OK \(6 tests\)|', $actual));

        // 更新がなければ実行されない
        ob_start();
        Maple4_DocTest::create()->run("{$pathname}/Test3/Test6/Test8/Test9.php", $options);
        $actual = ob_get_contents();
        ob_end_clean();

        $expected = '';
        $this->assertEquals($expected, $actual);

        // 更新されたので実行される
        clearstatcache();
        touch("{$pathname}/Test3/Test6/Test8/Test9.php");

        ob_start();
        Maple4_DocTest::create()->run("{$pathname}/Test3/Test6/Test8/Test9.php", $options);
        $actual = ob_get_contents();
        ob_end_clean();

        $this->assertEquals(1, preg_match('|OK \(6 tests\)|', $actual));
    }
}