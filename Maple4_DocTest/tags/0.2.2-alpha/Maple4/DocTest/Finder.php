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
 * PHPファイルをサーチしてリスト生成するクラス
 *
 * @category   Testing
 * @package    Maple4_DocTest
 * @author     TAKAHASHI Kunihiko <kunit@maple-project.com>
 * @copyright  2008 Maple Project
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @since      Class available since Release 0.2.0
 */
class Maple4_DocTest_Finder
{
    /**
     * 引数で指定されたディレクトリ(もしくはファイル)に対して
     * ファイルリストを返却
     *
     * @param array $options DocTestの動作オプション
     * @param string $regex ファイルリストに含めたい正規表現パターン
     * @param string $pathname ディレクトリ名もしくはファイル
     * @param string $basePathname 基準となるディレクトリ
     * @return array ファイルリスト
     * @access public
     */
    public function find($options, $regex, $pathname, $basePathname = null)
    {
        $fileUtils = new Maple4_Utils_File();

        if (isset($options['ignore'])) {
            $fileUtils->setIgnore($options['ignore']);
        }

        $pathname = $fileUtils->fixDirectorySeparator($pathname);

        $files = array();

        if (is_dir($pathname)) {
            $files = $fileUtils->findRecursive($pathname, $regex);
        } else if (file_exists($pathname) &&
                   preg_match($regex, $pathname)) {
            $files = array($fileUtils->fixDirectorySeparator($pathname));
        }

        $result = array();

        if (!is_array($files) || (count($files) < 1)) {
            return $result;
        }

        if (is_null($basePathname)) {
            $basePathname = $fileUtils->searchBasePathname($pathname);
        } else {
            $basePathname = $fileUtils->fixDirectorySeparator($basePathname);
        }

        foreach ($files as $filename) {
            $path = str_replace($basePathname, '', $filename);
            $result[$filename] = $fileUtils->removeHeadSlash($path);
        }

        return $result;
    }
}