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

require_once(dirname(dirname(__FILE__)) . '/Utils/File.php');

/**
 * ファイルの更新状況をチェックする
 *
 * @category   Testing
 * @package    Maple4_DocTest
 * @author     TAKAHASHI Kunihiko <kunit@maple-project.com>
 * @copyright  2008 Maple Project
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @since      Class available since Release 0.2.0
 */
class Maple4_DocTest_UpdateChecker
{
    /**
     * @var object Maple4_Utils_Fileのインスタンス
     * @access private
     */
    private $fileUtils = null;

    /**
     * コンストラクタ
     */
    public function __construct()
    {
        $this->fileUtils = new Maple4_Utils_File();
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
     * 引数で指定されたファイルリストに対して、更新状況をチェックする
     *
     * @param array $options DocTestの動作オプション
     * @param string $pathname ディレクトリ名もしくはファイル
     * @param array $files ファイルリスト
     * @return array 更新状況
     * @access public
     */
    public function check($options, $pathname, $files)
    {
        $result = array(
             'delete' => array(),
             'new' => array(),
             'update' => array(),
        );

        $options = new Maple4_Utils_Array($options);
        $compileDir = $options->compileDir;
        if (is_null($compileDir)) {
            return $result;
        }

        $forceCompile = $options->forceCompile;
        if (is_null($forceCompile)) {
            $forceCompile = false;
        }

        $filename = $this->makeStatusFilename($compileDir, $pathname);

        $lines = array();
        if ($forceCompile === true) {
            $this->fileUtils->unlink($filename);
        } else if (file_exists($filename)) {
            $buf = $this->fileUtils->read($filename);
            if (!is_null($buf)) {
                $lines = preg_split('|\n|', trim($buf));
            }
        }

        if (!is_array($lines)) {
            $lines = array();
        }

        $basePathname = $this->fileUtils->addTailSlash($this->fileUtils->searchBasePathname($pathname));

        $checkList = array();
        foreach ($lines as $line) {
            if (strlen(trim($line)) < 1) {
                continue;
            }

            list ($realpath, $mtime) = preg_split('|\t|', trim($line));
            $checkList[$realpath] = $realpath;
            $path = str_replace($basePathname, '', $realpath);

            if (!isset($files[$realpath])) {
                $result['delete'][$realpath] = $path;
            } else if (filemtime($realpath) > $mtime) {
                $result['update'][$realpath] = $path;
            }
        }

        $saveLines = null;
        foreach ($files as $realpath => $path) {
            if (!isset($checkList[$realpath])) {
                $result['new'][$realpath] = $path;
            }

            $saveLines .= sprintf("%s\t%s\n", $realpath, filemtime($realpath));
        }

        $this->fileUtils->write($filename, $saveLines);

        return $result;
    }

    /**
     * 更新状況を保存しているファイル名の生成
     *
     * @param string $compileDir コンパイルディレクトリ
     * @param string $pathname チェックしたいディレクトリ
     *                         もしくはファイル名
     * @return string ファイル名
     * @access private
     */
    private function makeStatusFilename($compileDir, $pathname)
    {
        return sprintf("%s/%s.txt", $this->fileUtils->removeTailSlash($compileDir), sha1($pathname));
    }
}
