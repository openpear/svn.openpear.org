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
require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/src/Maple4/DocTest/UpdateChecker.php');
require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/src/Maple4/Utils/File.php');
require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/src/Maple4/Utils/Array.php');

/**
 * ファイルの更新状況をチェックするクラスのテスト
 *
 * @category   Testing
 * @package    Maple4_DocTest
 * @author     TAKAHASHI Kunihiko <kunit@maple-project.com>
 * @copyright  2008 Maple Project
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @since      Class available since Release 0.2.0
 */
class Maple4_DocTest_UpdateCheckerTest extends PHPUnit_Framework_TestCase
{
    private $instance;
    private $baseDir;
    private $options;

    /**
     * テストの下準備
     */
    public function setUp()
    {
        $this->instance = new Maple4_DocTest_UpdateChecker();
        $this->baseDir = dirname(dirname(dirname(dirname(__FILE__))));
        $this->options = array(
            'compileDir' => "{$this->baseDir}/tmp/tests_c",
        );
    }

    /**
     * テストの後始末
     */
    public function tearDown()
    {
        $this->instance = null;
        $this->baseDir = null;
        $this->options = null;
    }

    /**
     * 更新が起こってないことをチェックできるか？
     */
    public function testCheckNoChange()
    {
        $pathname = $this->baseDir . '/fixtures/Maple4/DocTest/tests';

        $finder = new Maple4_Doctest_PHPFinder();
        $options = array('ignore' => '\.svn');
        $files = $finder->find($options, $pathname);
        $this->instance->check($this->options, $pathname, $files);

        $expected = array(
             'delete' => array(),
             'new' => array(),
             'update' => array(),
        );

        $files = $finder->find($options, $pathname);
        $this->assertEquals($expected, $this->instance->check($this->options, $pathname, $files));
    }

    /**
     * ファイルの更新が起こっていることをチェックできるか？
     */
    public function testCheckUpdate()
    {
        $pathname = $this->baseDir . '/fixtures/Maple4/DocTest/tests';

        $finder = new Maple4_Doctest_PHPFinder();
        $options = array('ignore' => '\.svn');
        $files = $finder->find($options, $pathname);
        $this->instance->check($this->options, $pathname, $files);

        $expected = array(
             'delete' => array(),
             'new' => array(),
             'update' => array(
                 "{$pathname}/Test1.php" => 'Test1.php',
                 "{$pathname}/Test2/Test5.php" => 'Test2/Test5.php',
                 "{$pathname}/Test3/Test6/Test8.php" => 'Test3/Test6/Test8.php',
                 "{$pathname}/Test3/Test6/Test8/Test9.php" => 'Test3/Test6/Test8/Test9.php',
              ),
        );

        $expected = $this->fixDirectorySeparator($expected);

        clearstatcache();
        touch("{$pathname}/Test1.php");
        touch("{$pathname}/Test2/Test5.php");
        touch("{$pathname}/Test3/Test6/Test8.php");
        touch("{$pathname}/Test3/Test6/Test8/Test9.php");

        $files = $finder->find($options, $pathname);
        $this->assertEquals($expected, $this->instance->check($this->options, $pathname, $files));

        $expected = array(
             'delete' => array(),
             'new' => array(
                 "{$pathname}/Test2/Test92.php" => 'Test2/Test92.php',
                 "{$pathname}/Test3/Test6/Test8/Test94.php" => 'Test3/Test6/Test8/Test94.php',
                 "{$pathname}/Test3/Test6/Test93.php" => 'Test3/Test6/Test93.php',
                 "{$pathname}/Test91.php" => 'Test91.php',
              ),
             'update' => array(),
        );

        $expected = $this->fixDirectorySeparator($expected);

        clearstatcache();
        touch("{$pathname}/Test91.php");
        touch("{$pathname}/Test2/Test92.php");
        touch("{$pathname}/Test3/Test6/Test93.php");
        touch("{$pathname}/Test3/Test6/Test8/Test94.php");

        $files = $finder->find($options, $pathname);
        $this->assertEquals($expected, $this->instance->check($this->options, $pathname, $files));

        $expected = array(
             'delete' => array(
                 "{$pathname}/Test2/Test92.php" => 'Test2/Test92.php',
                 "{$pathname}/Test3/Test6/Test8/Test94.php" => 'Test3/Test6/Test8/Test94.php',
                 "{$pathname}/Test3/Test6/Test93.php" => 'Test3/Test6/Test93.php',
                 "{$pathname}/Test91.php" => 'Test91.php',
              ),
             'new' => array(),
             'update' => array(),
        );

        $expected = $this->fixDirectorySeparator($expected);

        $fileUtils = new Maple4_Utils_File();

        $fileUtils->unlink("{$pathname}/Test91.php");
        $fileUtils->unlink("{$pathname}/Test2/Test92.php");
        $fileUtils->unlink("{$pathname}/Test3/Test6/Test93.php");
        $fileUtils->unlink("{$pathname}/Test3/Test6/Test8/Test94.php");

        $files = $finder->find($options, $pathname);
        $this->assertEquals($expected, $this->instance->check($this->options, $pathname, $files));
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
            $work = array();
            foreach ($value as $subKey => $subValue) {
                $subKey = $fileUtils->fixDirectorySeparator($subKey);
                $subValue = $fileUtils->fixDirectorySeparator($subValue);
                $work[$subKey] = $subValue;
            }
            $result[$key] = $work;
        }

        return $result;
    }
}