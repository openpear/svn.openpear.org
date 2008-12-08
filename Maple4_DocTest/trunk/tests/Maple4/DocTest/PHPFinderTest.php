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
require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/src/Maple4/DocTest/PHPFinder.php');
require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/src/Maple4/Utils/File.php');

/**
 * PHPファイルをサーチしてリスト生成するクラスのテスト
 *
 * @category   Testing
 * @package    Maple4_DocTest
 * @author     TAKAHASHI Kunihiko <kunit@maple-project.com>
 * @copyright  2008 Maple Project
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @since      Class available since Release 0.2.0
 */
class Maple4_DocTest_PHPFinderTest extends PHPUnit_Framework_TestCase
{
    private $instance;
    private $baseDir;

    /**
     * テストの下準備
     */
    public function setUp()
    {
        $this->instance = new Maple4_DocTest_PHPFinder();
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
     * ファイルリストが作成できるか？
     */
    public function testFind()
    {
        $options = array('ignore' => '\.svn');

        $expected = array();

        $this->assertEquals($expected, $this->instance->find($options, ''));

        $pathname = $this->baseDir . '/fixtures/Maple4/DocTest/tests';

        $expected = array(
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

        $expected = $this->fixDirectorySeparator($expected);

        $this->assertEquals($expected, $this->instance->find($options, $pathname));

        $expected = array(
             "{$pathname}/Test3/Test6/Test8/Test9.php" => 'Test3/Test6/Test8/Test9.php',
        );

        $expected = $this->fixDirectorySeparator($expected);

        $this->assertEquals($expected, $this->instance->find($options ,"{$pathname}/Test3/Test6/Test8/Test9.php", $pathname));

        $expected = array(
             "{$pathname}/Test2/Test5.php" => 'Test2/Test5.php',
             "{$pathname}/Test2/Test5/Test7.php" => 'Test2/Test5/Test7.php',
        );

        $expected = $this->fixDirectorySeparator($expected);

        $this->assertEquals($expected, $this->instance->find($options, "{$pathname}/Test2", $pathname));

        $expected = array(
             "{$pathname}/Test3/Test6/Test8/Test9.php" => 'Test3/Test6/Test8/Test9.php',
        );

        $expected = $this->fixDirectorySeparator($expected);

        $this->assertEquals($expected, $this->instance->find($options, "{$pathname}/Test3/Test6/Test8/Test9.php"));

        $expected = array(
             "{$pathname}/Test2/Test5.php" => 'Test2/Test5.php',
             "{$pathname}/Test2/Test5/Test7.php" => 'Test2/Test5/Test7.php',
        );

        $expected = $this->fixDirectorySeparator($expected);

        $this->assertEquals($expected, $this->instance->find($options, "{$pathname}/Test2"));
    }

    /**
     * ディレクトリ区切り文字をOSのものに統一する
     *
     * @param array $array 変換する配列
     * @return array 変換後の配列
     * @access private
     */
    private function fixDirectorySeparator($array)
    {
        $fileUtils = new Maple4_Utils_File();

        $result = array();
        foreach ($array as $key => $value) {
            $key = $fileUtils->fixDirectorySeparator($key);
            $value = $fileUtils->fixDirectorySeparator($value);
            $result[$key] = $value;
        }

        return $result;
    }
}