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
require_once('PHPUnit/TextUI/TestRunner.php');
require_once(dirname(dirname(__FILE__)) . '/Utils/File.php');
require_once(dirname(dirname(__FILE__)) . '/Utils/Class.php');
require_once(dirname(dirname(__FILE__)) . '/Utils/Array.php');

/**
 * テストケースを実行する
 *
 * @category   Testing
 * @package    Maple4_DocTest
 * @author     TAKAHASHI Kunihiko <kunit@maple-project.com>
 * @copyright  2008 Maple Project
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @since      Class available since Release 0.2.0
 */
class Maple4_DocTest_Runner
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
     * テストを実行する
     *
     * @param array $options DocTestの動作オプション
     * @param string $pathname テスト対象となるディレクトリ
     * @param array $testcases 実行するファイルリスト
     * @return string 実行結果
     * @access public
     */
    public function run($options, $pathname, $testcases)
    {
        // ファイル単体で指定された場合のための対策
        if (preg_match('|\.php$|', $pathname)) {
            $testcases = $this->filterTestCases($pathname, $testcases);
        }

        $suite = new PHPUnit_Framework_TestSuite();

        foreach ($testcases as $realpath => $path) {
            if (is_null($realpath) || is_null($path) ||
                !file_exists($realpath)) {
                continue;
            }

            require_once($realpath);

            $classname = $this->classUtils->toClassname($path);

            if (!is_null($classname)) {
                $suite->addTestSuite($classname);
            }
        }

        $parameters = array();

        $options = new Maple4_Utils_Array($options);

        if (!strstr(PHP_OS, "WIN") && $options->color) { 
            include_once(dirname(__FILE__) . '/Runner/ResultPrinter.php');
            $parameters['printer'] = new Maple4_DocTest_Runner_ResultPrinter();
        }

        if (!is_null($options->report) &&
            is_dir($options->report) &&
            file_exists($options->report)) {
            $parameters['reportDirectory'] = $options->report;
        }

        ob_start();
        PHPUnit_TextUI_TestRunner::run($suite, $parameters);
        $output = ob_get_contents();
        ob_end_clean();

        echo $output;

        if ($options->notify) {
            $notify = new Maple4_Utils_Array($options->notify);
            if ($notify->type === 'growl') {
                $this->growlNotify($notify, $output);
            }
        }
    }

    /**
     * ファイル単体で指定された場合はテストケースを絞る
     *
     * @param string $pathname テスト対象となるディレクトリ
     * @param array $testcases 実行するファイルリスト
     * @return array 絞り込んだテストケース
     * @access private
     */
    private function filterTestCases($pathname, $testcases)
    {
        $pathname = $this->fileUtils->fixDirectorySeparator($pathname);

        foreach ($testcases as $realpath => $path) {
            $original = $this->makeOriginalFilename($path);
            if (strstr($pathname, $original)) {
                $testcases = array($realpath => $path);
                break;
            }
        }

        return $testcases;
    }

    /**
     * テストケースファイルから元のPHPファイル名を生成する
     *
     * @param string $pathname テストケースのファイル名
     * @return string 元のPHPファイル名
     * @access private
     */
    private function makeOriginalFilename($pathname)
    {
        $result = preg_replace('|^Maple4_DocTest_|', '', $pathname);
        $result = preg_replace('|Test\.php$|', '', $result);
        $result = $this->fileUtils->removeHeadSlash($result) . '.php';

        $parts = preg_split('|_|', $this->classUtils->toClassname($result));

        $result = $this->fileUtils->fixDirectorySeparator(join('/', $parts) . '.php');

        return $result;
    }

    /**
     * Growlを利用する
     *
     * @param array $options DocTestの動作オプション(Notify部分)
     * @param string $output テスト結果
     * @access private
     */
    private function growlNotify($notify, $output)
    {
        @include_once 'Net/Growl.php';
        if (!class_exists('Net_Growl', false)) {
            return;
        }

        $appName = 'Maple4_DocTest';
        $notifications = array('Green', 'Red');

        $greenPriority = $notify->get('greePriority', 0);
        $greenSticky = $notify->get('greeStickey', false);
        $redPriority = $notify->get('redPriority', 2);
        $redSticky = $notify->get('redSticky', false);

        $status = 'Red';
        $title = $notify->get('title', 'Test Results');
        $description = 'No output';
        $notifyOptions = array(
            'priority' => $redPriority,
            'sticky' => $redSticky,
        );

        $output = preg_replace('|\033\[[\d;]+m|', '', $output);

        if (preg_match("|(OK \(\d+ test[s]{0,1}\))|m", $output, $matches)) {
            $status = 'Green';
            $description = $matches[1];
            $notifyOptions = array(
                'priority' => $greenPriority,
                'sticky' => $greenSticky,
            );
        } else if (preg_match("|(OK, but incomplete or skipped tests!\s*\nTests: .+\.)|m", $output, $matches)) {
            $status = 'Red';
            $description = $matches[1];
            $notifyOptions = array(
                'priority' => $redPriority,
                'sticky' => $redSticky,
            );
        } else if (preg_match("|(FAILURES!\s*\nTests: .+\.)|m", $output, $matches)) {
            $status = 'Red';
            $description = $matches[1];
            $notifyOptions = array(
                'priority' => $redPriority,
                'sticky' => $redSticky,
            );
        }

        set_error_handler(array($this, 'handler'));

        $application = new Net_Growl_Application($appName, $notifications, $notify->password);
        $growl = new Net_Growl($application);
        $growl->notify($status, $title, $description, $notifyOptions);
    }

    /**
     * Net_GrowlのStrict Errorだけを封じ込める
     *
     * 本当はこんなことしたくないのだが・・・
     * しかしUnitTestは E_ALL | E_STRICT で実行されるべき
     *
     * @param integer $errno エラーレベル
     * @param string $errstr エラーメッセージ
     * @param string $errfile エラーが発生したファイル名
     * @param string $errline エラーが発生した行数
     * @access public
     */
    public function handler($errno, $errstr, $errfile, $errline)
    {
        if (($errno === E_STRICT) &&
            preg_match('|Non-static method PEAR::getStaticProperty\(\) should not be called statically|', $errstr)) {
            return true;
        }

        return false;
    }
}
