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
 * @category   Utils
 * @package    Maple4_Utils
 * @copyright  2008 Maple Project
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @since      File available since Release 0.1.0
 */

require_once(dirname(dirname(__FILE__)) . '/Exception.php');

/**
 * ファイル関連のユーティリティークラス
 *
 * @category   Utils
 * @package    Maple4_Utils
 * @author     TAKAHASHI Kunihiko <kunit@maple-project.com>
 * @author     Kazunobu Ichihashi <bobchin@maple-project.com>
 * @copyright  2008 Maple Project
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @since      Class available since Release 0.1.0
 */
class Maple4_Utils_File
{
    const MARKER_FILENAME = '__BASEDIR__';

    /**
     * @var array 処理対象外パターンを保持する
     * @access private
     */
    private $ignore = array();

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
     * ディレクトリ区切り文字をOSのものに統一する
     *
     * @param string $pathname ディレクトリ文字列
     * @return string 置き換え後のディレクトリ文字列
     * @access public
     */
    public function fixDirectorySeparator($pathname)
    {
        return str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $pathname);
    }

    /**
     * 末尾のディレクトリ区切り文字を削除する
     *
     * @param string $pathname ディレクトリ文字列
     * @return string 削除後のディレクトリ文字列
     * @access public
     */
    public function removeTailSlash($pathname)
    {
        return rtrim($this->fixDirectorySeparator($pathname), DIRECTORY_SEPARATOR);
    }

    /**
     * 末尾にディレクトリ区切り文字を追加する
     *
     * @param string $pathname ディレクトリ文字列
     * @return string 追加後のディレクトリ文字列
     * @access public
     */
    public function addTailSlash($pathname)
    {
        return $this->removeTailSlash($pathname) . DIRECTORY_SEPARATOR;
    }

    /**
     * 先頭のディレクトリ区切り文字を削除する
     *
     * @param string $pathname ディレクトリ文字列
     * @return string 削除後のディレクトリ文字列
     * @access public
     */
    public function removeHeadSlash($pathname)
    {
        return ltrim($this->fixDirectorySeparator($pathname), DIRECTORY_SEPARATOR);
    }

    /**
     * 先頭にディレクトリ区切り文字を追加する
     *
     * @param string $pathname ディレクトリ文字列
     * @return string 追加後のディレクトリ文字列
     * @access public
     */
    public function addHeadSlash($pathname)
    {
        return DIRECTORY_SEPARATOR . $this->removeHeadSlash($pathname);
    }

    /**
     * 指定した文字列を処理対象外とする
     *
     * @param mixed $str 処理対象外とする文字列
     * @access public
     */
    public function setIgnore($path)
    {
        if (is_array($path)) {
            $this->ignore = array_merge($this->ignore, $path);
        } else {
            $this->ignore[] = $path;
        }
    }

    /**
     * 処理対象外リストをクリアする
     *
     * @access public
     */
    public function clearIgnore()
    {
        $this->ignore = array();
    }

    /**
     * 処理対象外かどうか？
     *
     * @param string $pathname ディレクトリ名
     * @return boolean 処理対象外かどうか
     * @access public
     */
    public function isIgnore($pathname)
    {
        $match = false;
        foreach ($this->ignore as $ignore) {
            if (preg_match("|{$ignore}|", $pathname)) {
                $match = true;
                break;
            }
        }

        return $match;
    }

    /**
     * 基準ディレクトリを返却
     *
     * 基準となるディレクトリに設置されているマーカーファイルを探す
     *
     * @param string $pathname ディレクトリ名もしくはファイル
     * @return string 基準となるディレクトリ 
     * @access public
     */
    public function searchBasePathname($pathname)
    {
        if (is_dir($pathname)) {
            $path = $pathname;
        } else {
            $path = dirname($pathname);
        }

        while (!$this->isTopDir($path)) {
            $marker = $this->addTailSlash($path) . self::MARKER_FILENAME;
            if (file_exists($marker)) {
                break;
            }
            $path = dirname($path);
        }

        return $this->removeTailSlash($path);
    }

    /**
     * トップディレクトリか？
     *
     * @param $pathname ディレクトリ名
     * @return boolean トップディレクトリかどうか
     * @access private
     */
    private function isTopDir($pathname)
    {
        if (strstr(PHP_OS, "WIN")) {
            $result = preg_match('|^[A-Za-z]+:\\\\$|', $pathname);
        } else {
            $result = ($this->removeHeadSlash($pathname) === '');
        }

        return $result;
    }

    /**
     * ディレクトリとファイルのリストを取得する
     *
     * @param string $pathname ディレクトリ名
     * @return array ディレクトリとファイルの配列
     * @access public
     */
    public function ls($pathname)
    {
        $dirs = array();
        $files = array();

        if (!is_dir($pathname) || (!$dh = opendir($pathname))) {
            return array($dirs, $files);
        }

        while (($filename = readdir($dh)) !== false) {
            if (preg_match("/^[.]{1,2}$/", $filename)) {
                continue;
            }

            $realpath = $this->addTailSlash($pathname) . $filename;

            if ($this->isIgnore($realpath)) {
                continue;
            }

            if (is_dir($realpath)) {
                $dirs[] = $realpath;
            } else {
                $files[] = $realpath;
            }
        }

        closedir($dh);

        sort($dirs);
        sort($files);

        return array($dirs, $files);
    }

    /**
     * 指定したディレクトリのファイルリストを取得する
     *
     * @param string $pathname ディレクトリ名
     * @param string $regex 取得するファイル名の正規表現
     * @param function $callback コールバック
     * @return array ファイルの配列
     * @access public
     */
    public function find($pathname, $regex = null, $callback = null)
    {
        list (, $files) = $this->ls($pathname);

        if (is_null($regex)) {
            if (is_callable($callback)) {
                $files = array_map($callback, $files);
            }
            return $files;
        }

        $result = array();
        foreach ($files as $filename) {
            if (preg_match($regex, $filename)) {
                if (is_callable($callback)) {
                    $filename = call_user_func($callback, $filename);
                }
                $result[] = $filename;
            }
        }

        return $result;
    }

    /**
     * 指定したディレクトリ以下のファイルリストを取得する
     *
     * サブディレクトリがある場合は再帰的に取得する
     *
     * @param string $pathname ディレクトリ名
     * @param string $regex 取得するファイル名の正規表現
     * @param function $callback コールバック
     * @return array ファイルの配列
     * @access public
     */
    public function findRecursive($pathname, $regex = null, $callback = null)
    {
        list ($dirs,) = $this->ls($pathname);

        $found = $this->find($pathname, $regex, $callback);

        foreach ($dirs as $dir) {
            $found = array_merge($found, $this->findRecursive($dir, $regex, $callback));
        }

        sort($found);
        reset($found);

        return $found;
    }

    /**
     * ファイルを削除する
     *
     * @param string $filename ファイル名
     * @return boolean 処理結果
     * @access public
     */
    public function unlink($filename)
    {
        $result = true;
        if (file_exists($filename)) {
            $result = unlink($filename);
        }

        return $result;
    }

    /**
     * ディレクトリを作成する。
     *
     * 複数階層のディレクトリを指定した場合に、
     * 途中のディレクトリも自動的に作成する。
     *
     * @param string $pathname 作成するディレクトリ名
     * @param int $mode 権限
     * @param int $umask umaskの値
     * @return boolean 処理結果
     * @access public
     */
    public function makeDir($pathname, $mode = 0777, $umask = 0)
    {
        umask($umask);

        $result = true;
        if (!file_exists($pathname)) {
            $result = mkdir($pathname, $mode, true);
        }

        return $result;
    }

    /**
     * ディレクトリを削除する
     *
     * 指定したディレクトリより下のディレクトリ・ファイルも
     * 自動的に削除する。
     *
     * @param string $pathname 削除するディレクトリ名
     * @return boolean 処理結果
     * @access public
     */
    public function removeDir($pathname)
    {
        list ($dirs, $files) = $this->ls($pathname);

        array_map(array($this, 'unlink'), $files);
        array_map(array($this, 'removeDir'), $dirs);

        $result = true;
        if (file_exists($pathname)) {
            $result = rmdir($pathname);
        }

        return $result;
    }

    /**
     * ファイルを読み込む
     *
     * @param string $filename ファイル名
     * @return string ファイルの内容
     * @access public
     */
    public function read($filename)
    {
        $result = null;

        if (!file_exists($filename)) {
            throw new Maple4_Exception("file not found({$filename})");
        }

        if (!is_readable($filename)) {
            throw new Maple4_Exception("file not readable({$filename})");
        }

        if (version_compare('6.0.0', phpversion(), '<=')) {
            $result = @file_get_contents($filename, FILE_USE_INCLUDE_PATH);
        } else {
            $result = @file_get_contents($filename, true);
        }

        if ($result === false) {
            $result = null;
        }

        return $result;
    }

    /**
     * ファイルに書き込む
     *
     * 指定した内容をファイルに上書きで書き込む。
     * 途中のフォルダが存在しない場合は自動的に作成する。
     *
     * @param string $filename  ファイル名
     * @param string $buf  書き込む内容
     * @param string $mode  書き込みモード
     * @return boolean
     * @access public
     */
    public function write($filename, $buf, $mode = "wb")
    {
        if (file_exists($filename) && !is_writable($filename)) {
            throw new Maple4_Exception("file not writable({$filename})");
        }

        $this->makeDir(dirname($filename));

        if (!($fh = fopen($filename, $mode))) {
            return false;
        }

        if (!fwrite($fh, $buf)) {
            return false;
        }

        if (!fclose($fh)) {
            return false;
        }

        return true;
    }
}
