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

/**
 * クラス関連のユーティリティークラス
 *
 * @category   Utils
 * @package    Maple4_Utils
 * @author     TAKAHASHI Kunihiko <kunit@maple-project.com>
 * @copyright  2008 Maple Project
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @since      Class available since Release 0.1.0
 */
class Maple4_Utils_Class
{
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
     * 指定されたファイル名に対するクラス名を返却
     *
     * 返却されるクラス名は小文字に変換後返却される
     *
     * オプション
     *  ucfirst:   クラス名の各パートの先頭を大文字にするか
     *  namespace: 変換時に使用する擬似名前空間
     *
     * @param string $filename クラス名に変換したいファイル名
     * @param array $options 変換時に使用するオプション
     * @return string クラス名
     * @access public
     */
    public function toClassname($filename, $options = array())
    {
        $doUcfirst = true;
        if (isset($options['ucfirst']) &&
            !is_null($options['ucfirst'])) {
            $doUcfirst = $options['ucfirst'];
        }

        $namespace = '';
        if (isset($options['namespace']) &&
            !is_null($options['namespace'])) {
            $namespace = $options['namespace'];
        }

        $result = null;
        if (!preg_match('|\.php|', $filename)) {
            return $result;
        }

        if ($namespace) {
            $pathname = join('/', preg_split('|_|', $namespace));
            $filename = "{$pathname}/{$filename}";
        }

        $filename = preg_replace('|\.php|', '', $filename);
        $parts = preg_split('|[\\\\/]|', $filename);

        if ($doUcfirst) {
            $result = join('_', array_map('ucfirst', $parts));
        } else {
            $result = strtolower(join('_', $parts));
        }

        return $result;
    }
}
