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

/**
 * PHPファイルからDocTestコメントを抜き出す
 *
 * @category   Testing
 * @package    Maple4_DocTest
 * @author     TAKAHASHI Kunihiko <kunit@maple-project.com>
 * @copyright  2008 Maple Project
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @since      Class available since Release 0.2.0
 */
class Maple4_DocTest_Parser
{
    /**
     * PHPファイルからDocTestコメントを抜き出す
     *
     * @param string $realpath 対象となるPHPファイル(フルパス)
     * @param string $path 対象となるPHPファイル
     * @return string 抜き出した文字列
     * @access public
     */
    public function parse($realpath, $path)
    {
        $result = array();

        if (!file_exists($realpath)) {
            return $result;
        }

        include_once($realpath);

        $classname = Maple4_Utils_Class::create()->toClassname($path);

        $docComments = $this->getDocComments($classname);

        foreach ($docComments as $methodname => $methodComment) {
            $this->getTestData($result, $methodComment, $methodname);
        }

        return $result;
    }

    /**
     * クラスおよびメソッドに対するDocコメントを取得
     *
     * 返却値はクラスおよび各メソッドに対するDocコメント
     *
     * @param string $classname クラス名
     * @return array Docコメントの配列
     * @access private
     */
    private function getDocComments($classname)
    {
        $refClass = new ReflectionClass($classname);

        $result = array();

        if ($comment = $refClass->getDocComment()) {
            $result[''] = $this->removeCommentStr($comment);
        }

        // 親クラスのコメントは削除する
        $method_comments = $this->getDocCommentsOfMethods($refClass);

        while($refClass = $refClass->getParentClass()) { 
            $parent_comments = $this->getDocCommentsOfMethods($refClass); 
            foreach ($parent_comments as $method => $comment) {
                if (isset($method_comments[$method]) &&
                    $method_comments[$method] == $comment) {
                    unset($method_comments[$method]);
                }
            }
        }

        $result = array_merge($result, $method_comments);

        return $result;
    }

    /**
     * 文字列からコメント文字列を除去
     *
     * 正規表現があまりいけてないような気がするので
     * 後で直す・・・
     *
     * @param string $str 元の文字列
     * @return string コメント文字列を除去した文字列
     * @access private
     */
    private function removeCommentStr($str)
    {
        $str = preg_replace('|^\s*/\*\*|m', '', $str);
        $str = preg_replace('|^\s*\*/|m', '', $str);
        $str = preg_replace('|^\s*\*[ ]{1}|m', '', $str);
        $str = preg_replace('|^\s*\*[ ]{0}|m', '', $str);

        return trim($str);
    }

    /**
     * メソッドのDocコメントを取得する
     *
     * @param object $refClass リフレクションクラスのインスタンス
     * @return array メソッドに対するコメントの配列
     * @access private
     */
    private function getDocCommentsOfMethods($refClass)
    {
        $result = array();
        foreach ($refClass->getMethods() as $refMethod) {
            if ($comment = $refMethod->getDocComment()) {
                $result[$refMethod->getName()]
                    = $this->removeCommentStr($comment);
            }
        }

        return $result;
    }

    /**
     * 指定した文字列からテストに関する情報のみを抜き出す
     *
     * 第1引数で結果を組み立てていく
     *
     * @param array &$result テスト情報の配列
     * @param string $str Docコメント文字列
     * @param string $methodname メソッド名
     * @access private
     */
    private function getTestData(&$result, $str, $methodname = null)
    {
        $methodname = strtolower($methodname);

        if (preg_match_all('|(#test (.*?))?<code>(.*?)</code>|s', $str, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $userdefine = trim($match[2]);
                if (($methodname === '') && ($userdefine === '')) {
                    throw new Maple4_Exception('missing testname');
                    break;
                }

                $testname = $this->createTestName($methodname, $userdefine);
                $body = trim($match[3]);

                if (preg_match('|#f\(|', $body)) {
                    if (!$methodname) {
                        throw new Maple4_Exception('missing testname');
                        break;
                    }

                    $body = preg_replace('|#f\(|', "\$this->obj->{$methodname}(", $body);
                }

                if (isset($result[$testname])) {
                    $result[$testname]['body'] .= "\n\n" . $body;
                } else {
                    $result[$testname] = array(
                        'method' => $methodname,
                        'userdefine' => $userdefine,
                        'body' => $body,
                    );
                }
            }
        }

        return $result;
    }

    /**
     * テスト名を生成する
     *
     * @param string $methodname テスト対象のメソッド
     * @param string $userdefine ユーザが記述したテスト名
     * @return string 生成されたテスト名
     * @access private
     */
    private function createTestName($methodname, $userdefine)
    {
        $specialKeys = array('__noop', '__setup', '__teardown');
        if (in_array(strtolower($userdefine), $specialKeys)) {
            $testname = strtolower($userdefine);
        } else {
            $testname = $methodname;
            if ($userdefine) {
                $testname .= '_' . $userdefine;
            }
        }

        return $testname;
    }
}