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

require_once(dirname(dirname(__FILE__)) . '/Utils/Class.php');
require_once(dirname(dirname(__FILE__)) . '/Utils/File.php');

/**
 * DocTestコメントから生成された文字列からテストケースを生成する
 *
 * @category   Testing
 * @package    Maple4_DocTest
 * @author     TAKAHASHI Kunihiko <kunit@maple-project.com>
 * @copyright  2008 Maple Project
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @since      Class available since Release 0.2.0
 */
class Maple4_DocTest_Builder
{
    /**
     * @var object Maple4_Utils_Fileのインスタンス
     * @access private
     */
    private $fileUtils = null;

    /**
     * @static string インデントで使用する空白の数
     * @access private
     */
    static private $indentWidth = 4;

    /**
     * コンストラクタ
     */
    public function __construct()
    {
        $this->fileUtils = new Maple4_Utils_File();
    }

    /**
     * DocTestコメントから抜き出された文字列からテストケースを生成
     *
     * @param string $realpath 対象となるPHPファイル(フルパス)
     * @param string $path 対象となるPHPファイル
     * @param array $comments DocTestコメントから抜き出された
     *                        文字列の配列
     * @return string 生成したテストケース
     * @access public
     */
    public function build($realpath, $path, $comments)
    {
        if (is_null($realpath) || is_null($path) ||
            !is_array($comments)) {
            return null;
        }

        $definition = $this->createTestClassDefinition($realpath, $path, $comments);
        if (is_null($definition)) {
            return null;
        }

        $template = $this->getFileTemplate();
        return rtrim(sprintf($template, $realpath, rtrim($definition)));
    }

    /**
     * テストで使用するクラス定義を生成する
     *
     * @param string $realpath 対象となるPHPファイル(フルパス)
     * @param string $path 対象となるPHPファイル
     * @param array $comments DocTestコメントから抜き出された
     *                        文字列の配列
     * @return string 生成されたクラス定義
     * @access private
     */
    private function createTestClassDefinition($realpath, $path, $comments)
    {
        $definition = null;
        $existsSetUp = false;
        $existsTearDown = false;

        foreach ($comments as $testname => $commentData) {
            $body = $commentData['body'];
            $method = $commentData['method'];

            if ($testname === '__noop') {
                $definition .= $this->concatIndent($body, self::$indentWidth) . "\n\n";
            } else {
                $definition .= $this->createMethodDefinition($testname, $commentData);
            }

            if ($testname === '__setup') {
                $existsSetUp = true;
            } else if ($testname === '__teardown') {
                $existsTearDown = true;
            }
        }

        if (is_null($definition)) {
            return null;
        }

        if (!$existsSetUp && !$existsTearDown) {
            $body = $this->getSpecialMethodTemplate();
            $definition = $this->concatIndent($body, self::$indentWidth) . "\n\n" . $definition;
        }

        $replaceStrings = $this->getReplaceStrings();

        $classname = Maple4_Utils_Class::create()->toClassname($path);
        $replaceStrings['class'] = "{$classname}()";

        foreach ($replaceStrings as $from => $to) {
            $definition = preg_replace("|#{$from}|", $to, $definition);
        }

        $template = $this->getClassTemplate();
        return rtrim(sprintf($template, $classname, rtrim($definition)));
    }

    /**
     * 文字列の各行の先頭にインデントを挿入
     *
     * 文字列の各行に引数で指定された数だけのインデントを挿入
     *
     * @param string $str 元の文字列
     * @param integer $indent インデントの数
     * @return string インデント挿入後の文字列
     * @access private
     */
    private function concatIndent($str, $indent = null)
    {
        if ($indent === null) {
            $indent = self::$indentWidth * 2;
        }

        $lines = preg_split("|\n|", $str);
        $lines = array_map('rtrim', $lines);

        $hereDocumentString = null;
        $quoteString = null;

        foreach ($lines as $key => $line) {
            if ($hereDocumentString || $quoteString) {
                $lines[$key] = rtrim($line);

                if ($hereDocumentString &&
                    preg_match("|^{$hereDocumentString}[;,]*|", $line)) {
                    $hereDocumentString = null;
                }

                if ((($quoteString == "'") &&
                     preg_match('/(^\'|[^\\\\]?\')/', $line)) ||
                    (($quoteString == '"') &&
                     preg_match('/(^"|[^\\\\]?")/', $line))) {
                    $quoteString = null;
                }
            } else {
                $lines[$key] = rtrim(str_repeat(chr(32), $indent) . $line);

                if (preg_match('|<<<(.+)|', $line, $matches)) {
                    $hereDocumentString = $matches[1];
                } else if (preg_match_all('/(^\'|[^\\\\]?\')/', $line, $matches, PREG_SET_ORDER) &&
                           (count($matches) == 1)) {
                    $quoteString = "'";
                } else if (preg_match_all('/(^"|[^\\\\]?")/', $line, $matches, PREG_SET_ORDER) &&
                           (count($matches) == 1)) {
                    $quoteString = '"';
                }
            }
        }

        return rtrim(join("\n", $lines));
    }

    /**
     * 文字列からメソッドを生成する
     *
     * @param string $testname テスト名
     * @param array $commentData コメント情報
     * @return string メソッド定義
     * @access private
     */
    private function createMethodDefinition($testname, $commentData)
    {
        $specialKeys = array(
            '__setup'    => 'setUp',
            '__teardown' => 'tearDown',
        );

        $methodname = $commentData['method'];
        $userdefine = $commentData['userdefine'];
        $header = ($userdefine) ? ($userdefine) : $methodname;
        $body = sprintf("// %s\n%s", $header, $commentData['body']);

        if (in_array(strtolower($userdefine), array_keys($specialKeys))) {
            $methodname = $specialKeys[strtolower($userdefine)];
        } else {
            $methodname = sprintf('test%s', ucfirst($testname));
        }

        $body = $this->concatIndent($body, self::$indentWidth * 2);

        $template = $this->getMethodTemplate();
        return rtrim(sprintf($template, $methodname, $body)) ."\n\n";
    }

    /**
     * メソッドのテンプレートを返却
     *
     * @return string メソッドのテンプレート
     * @access private
     */
    private function getMethodTemplate()
    {
        $filename = dirname(__FILE__) . '/Builder/method.php';
        return $this->fileUtils->read($filename);
    }

    /**
     * スペシャルメソッド(setUp/TearDown)のテンプレートを返却
     *
     * @return string スペシャルメソッドのテンプレート
     * @access private
     */
    private function getSpecialMethodTemplate()
    {
        $filename = dirname(__FILE__) . '/Builder/specialMethod.php';
        return $this->fileUtils->read($filename);
    }

    /**
     * クラスのテンプレートを返却
     *
     * @return string クラスのテンプレート
     * @access public
     */
    public function getClassTemplate()
    {
        $filename = dirname(__FILE__) . '/Builder/class.php';
        return $this->fileUtils->read($filename);
    }

    /**
     * テストケースファイルのテンプレートを返却
     *
     * @return string テストケースクラスのテンプレート
     * @access public
     */
    public function getFileTemplate()
    {
        $filename = dirname(__FILE__) . '/Builder/file.php';
        return $this->fileUtils->read($filename);
    }

    /**
     * メソッドの省略可能文字列を返却
     *
     * @return string 省略文字列の配列
     * @access public
     */
    public function getReplaceStrings()
    {
        return array(
            'eq\('      => '$this->assertEquals(',
            'ne\('      => '$this->assertNotEquals(',
            'true\('    => '$this->assertTrue(',
            'false\('   => '$this->assertFalse(',
            'null\('    => '$this->assertNull(',
            'notnull\(' => '$this->assertNotNull(',
        );
    }
}