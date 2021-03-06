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
 * @since      File available since Release 0.2.0
 */

/**
 * 配列関連のユーティリティークラス
 *
 * @category   Utils
 * @package    Maple4_Utils
 * @author     TAKAHASHI Kunihiko <kunit@maple-project.com>
 * @copyright  2008 Maple Project
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @since      Class available since Release 0.2.0
 */
class Maple4_Utils_Array
{
    /**
     * @var array 各要素のデフォルト値
     */
    private $defaults = array();

    /**
     * @var array 処理する配列
     */
    private $array = array();

    /**
     * コンストラクタ
     *
     * @params array $array 処理する配列
     */
    public function __construct($array = array())
    {
        $this->array = $array;
    }

    /**
     * fluent interface(流れるようなインタフェースで使用するための
     * スタティックコンストラクタ
     *
     * @params array $array 処理する配列
     * @return object このオブジェクト自身
     */
    static public function create($array = array())
    {
        return new self($array);
    }

    /**
     * 配列から安全に値を取得する
     *
     * @param string $key 配列要素
     * @param mixed $default 値がなかった場合のデフォルト値
     * @return mixed 取得した値
     * @access public
     */
    public function get($key, $default = null)
    {
        if (is_array($this->array) && isset($this->array[$key])) {
            $result = $this->array[$key];
        } else {
            $result = $default;
        }

        return $result;
    }

    /**
     * 配列に値をセットする
     *
     * @param string $key 配列要素
     * @param mixed $value 要素に対する値
     * @return object このオブジェクト自身
     * @access public
     */
    public function set($key, $value = null)
    {
        $this->array[$key] = $value;
        return $this;
    }

    /**
     * 配列ということを隠蔽したアクセス
     *
     * @param mixed $key 配列要素
     * @return mixed 要素に対する値
     */
    public function __get($key)
    {
        $default = null;
        if (isset($this->default[$key])) {
            $default = $this->default[$key];
        }

        return $this->get($key, $default);
    }

    /**
     * 配列ということを隠蔽したアクセス
     *
     * @param mixed $key 配列要素
     * @param mixed $value 要素に対する値
     */
    public function __set($key, $value)
    {
        $this->set($key, $value);
    }

    /**
     * 要素に対してデフォルト値をセット
     *
     * @param mixed $key 配列要素
     * @param mixed $default 要素に対するデフォルト値
     * @return object このオブジェクト自身
     */
    public function setDefault($key, $default = null)
    {
        $this->array[$key] = $default;

        return $this;
    }
}
