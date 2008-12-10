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
 * @since      File available since Release 0.1.0
 */

require_once('PHPUnit/Framework.php');
require_once('PHPUnit/TextUI/TestRunner.php');
require_once(dirname(__FILE__) . '/Utils/File.php');
require_once(dirname(__FILE__) . '/Utils/Class.php');
require_once(dirname(__FILE__) . '/Utils/Array.php');
require_once(dirname(__FILE__) . '/DocTest/PHPFinder.php');
require_once(dirname(__FILE__) . '/DocTest/UpdateChecker.php');
require_once(dirname(__FILE__) . '/DocTest/Parser.php');
require_once(dirname(__FILE__) . '/DocTest/Builder.php');
require_once(dirname(__FILE__) . '/DocTest/TestCaseFinder.php');
require_once(dirname(__FILE__) . '/DocTest/Runner.php');

/**
 * 指定されたディレクトリにあるPHPファイルに対するテストを実行
 *
 * @category   Testing
 * @package    Maple4_DocTest
 * @author     TAKAHASHI Kunihiko <kunit@maple-project.com>
 * @copyright  2008 Maple Project
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @since      Class available since Release 0.1.0
 */
class Maple4_DocTest
{
    /**
     * @var object Maple4_Utils_Fileのインスタンス
     * @access private
     */
    private $fileUtils;

    /**
     * @var object Maple4_Utils_Classのインスタンス
     * @access private
     */
    private $classUtils;

    /**
     * コンストラクタ
     */
    public function __construct()
    {
        $this->fileUtils = new Maple4_Utils_File();
        $this->classUtils = new Maple4_Utils_Class();
    }

    /**
     * fluent interface(流れるようなインタフェースで使用するための
     * スタティックコンストラクタ
     *
     * @return object このオブジェクト自身
     */
    static public function create()
    {
        return new self();
    }

    /**
     * 指定されたディレクトリにあるPHPファイルに対するテストを実行
     *
     * @param string $pathname テスト対象となるディレクトリ
     * @param array $options テストで使用するオプション
     * @return string 実行結果
     * @access public
     */
    public function run($pathname, $options)
    {
        if (!isset($options['compileDir'])) {
            throw new Maple4_Exception('compileDir not set');
        }

        $phpFiles = Maple4_DocTest_PHPFinder::create()->find($options, $pathname);
        $updateFiles = Maple4_DocTest_UpdateChecker::create()->check($options, $pathname, $phpFiles);
        $this->updateTestCases($options, $updateFiles);

        if ($this->isUpdated($options, $updateFiles)) {
            $testcases = $this->findTestCases($options, $pathname, $phpFiles);
        } else {
            $testcases = array();
        }

        if (count($testcases) > 0) {
            Maple4_DocTest_Runner::create()->run($options, $pathname, $testcases);
        }
    }

    /**
     * テストケースファイルの更新を行う
     *
     * @param array $options テストで使用するオプション
     * @param array $updateFiles 更新ファイルリスト
     * @access private
     */
    private function updateTestcases($options, $updateFiles)
    {
        $compileDir = $options['compileDir'];
        $this->fileUtils->makeDir($compileDir);

        if (isset($updateFiles['delete']) &&
            (count($updateFiles['delete'] > 0))) {
            foreach ($updateFiles['delete'] as $realpath => $path) {
                $filename = $this->toTestCase($options, $path);
                $this->fileUtils->unlink($filename);
            }
        }

        $parser = new Maple4_DocTest_Parser();
        $builder = new Maple4_DocTest_Builder();

        foreach (array('new', 'update') as $key) {
            if (!isset($updateFiles[$key])) {
                continue;
            }

            foreach ($updateFiles[$key] as $realpath => $path) {
                $comments = $parser->parse($realpath, $path);
                $data = $builder->build($realpath, $path, $comments);
                if (is_null($data)) {
                    continue;
                }

                $filename = $this->toTestCase($options, $path);
                $this->fileUtils->write($filename, $data);
            }
        }
    }

    /**
     * テストケースのファイルリストを取得する
     *
     * @param array $options テストで使用するオプション
     * @param string $pathname PHPファイル名
     * @param array $files PHPFinderが検索したファイルリスト
     * @return array テストケースのファイルリスト
     * @access private
     */
    private function findTestCases($options, $pathname, $files)
    {
        // ファイル単体で指定された場合のための対策
        // 余計なファイル探索が走らないように
        if (preg_match('|\.php$|', $pathname)) {
            $testcases = Maple4_DocTest_TestCaseFinder::create()->find($options, $this->convertTestCase($options, $pathname, $files));
        } else {
            $list = Maple4_DocTest_TestCaseFinder::create()->find($options, $options['compileDir']);

            $pickup = array();
            foreach ($files as $realpath => $path) {
                $filename = $this->toTestCase($options, $path);
                if (isset($list[$filename])) {
                    $pickup[$filename] = $list[$filename];
                }
            }

            $testcases = $pickup;
        }

        return $testcases;
    }

    /**
     * 指定されたPHPファイルからテストケースファイル名を生成する
     *
     * @param array $options テストで使用するオプション
     * @param string $pathname PHPファイル名
     * @param array $files PHPFinderが検索したファイルリスト
     * @return string テストケースのファイル名
     * @access private
     */
    private function convertTestCase($options, $pathname, $files)
    {
        $pathname = $this->fileUtils->fixDirectorySeparator($pathname);

        $result = null;
        foreach ($files as $realpath => $path) {
            $realpath = $this->fileUtils->fixDirectorySeparator($realpath);
            if ($pathname === $realpath) {
                $result = $this->toTestCase($options, $path);
                break;
            }
        }

         return $result;
    }

    /**
     * PHPファイル名からテストケースファイル名を生成する
     *
     * @param array $options テストで使用するオプション
     * @param string $pathname PHPファイル名
     * @return string テストケースのファイル名
     * @access private
     */
    private function toTestCase($options, $pathname)
    {
        $compileDir = $options['compileDir'];
        $classname = $this->classUtils->toClassname($pathname);
        $filename = "{$compileDir}/Maple4_DocTest_{$classname}Test.php";

        return $this->fileUtils->fixDirectorySeparator($filename);
    }

    /**
     * 更新されたファイルがあるか？
     *
     * @param array $options テストで使用するオプション
     * @param array $files UpdateCheckerが生成したファイルリスト
     * @return boolean 更新されたファイルがあるか？
     * @access private
     */
    private function isUpdated($options, $files)
    {
        if (!isset($options['forceRun']) || $options['forceRun']) {
            return true;
        }

        $files = new Maple4_Utils_Array($files);

        if (!is_array($files->new) ||
            !is_array($files->update) ||
            !is_array($files->delete)) {
            return false;
        }

        return ((count($files->new) > 0) ||
                (count($files->update) > 0) ||
                (count($files->delete) > 0));
    }
}
