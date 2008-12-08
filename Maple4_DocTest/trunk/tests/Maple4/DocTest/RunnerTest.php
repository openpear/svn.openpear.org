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
require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/src/Maple4/DocTest/Runner.php');

/**
 * テストケースを実行するクラスのテスト
 *
 * @category   Testing
 * @package    Maple4_DocTest
 * @author     TAKAHASHI Kunihiko <kunit@maple-project.com>
 * @copyright  2008 Maple Project
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @since      Class available since Release 0.2.0
 */
class Maple4_DocTest_RunnerTest extends PHPUnit_Framework_TestCase
{
    private $instance;
    private $baseDir;

    /**
     * テストの下準備
     */
    public function setUp()
    {
        $this->instance = new Maple4_DocTest_Runner();
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
     * テストが実行できるか？
     */
    public function testRun()
    {
        $pathname = $this->baseDir . '/fixtures/Maple4/DocTest/testcases';

        $testcases = array(
             "{$pathname}/Test1/Test4Test.php" => 'Test1/Test4Test.php',
             "{$pathname}/Test1Test.php" => 'Test1Test.php',
             "{$pathname}/Test2/Test5/Test7Test.php" => 'Test2/Test5/Test7Test.php',
             "{$pathname}/Test2/Test5Test.php" => 'Test2/Test5Test.php',
             "{$pathname}/Test2Test.php" => 'Test2Test.php',
             "{$pathname}/Test3/Test6/Test8/Test9Test.php" => 'Test3/Test6/Test8/Test9Test.php',
             "{$pathname}/Test3/Test6/Test8Test.php" => 'Test3/Test6/Test8Test.php',
             "{$pathname}/Test3/Test6Test.php" => 'Test3/Test6Test.php',
             "{$pathname}/Test3Test.php" => 'Test3Test.php',
        );

        $options = array('color' => true);

        ob_start();
        $this->instance->run($options, $pathname, $testcases);
        $actual = ob_get_contents();
        ob_end_clean();

        $this->assertEquals(1, preg_match('|OK \(9 tests\)|', $actual));
    }
}