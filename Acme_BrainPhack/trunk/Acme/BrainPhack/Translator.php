<?php
/*
 *   Copyright (c) 2010 msakamoto-sf <msakamoto-sf@users.sourceforge.net>
 *
 *   Licensed under the Apache License, Version 2.0 (the "License");
 *   you may not use this file except in compliance with the License.
 *   You may obtain a copy of the License at
 *
 *       http://www.apache.org/licenses/LICENSE-2.0
 *
 *   Unless required by applicable law or agreed to in writing, software
 *   distributed under the License is distributed on an "AS IS" BASIS,
 *   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *   See the License for the specific language governing permissions and
 *   limitations under the License.*
 */

/**
 * @package Acme_BrainPhack
 * @author msakamoto-sf <msakamoto-sf@users.sourceforge.net>
 * @version $Id$
 */

/**
 * BrainF*ck Translator
 *
 * @author msakamoto-sf <sakamoto-gsyc-3s@glamenv-septzen.net>
 * @package Acme_BrainPhack
 * @since 0.0.1
 */
class Acme_BrainPhack_Translator
{
    // {{{ translate()

    function translate($map, $src)
    {
        $conv = '';

        $src_len = mb_strlen($src);
        if (0 == $src_len) {
            return $conv;
        }

        // expand map to convert table
        $needles = array();
        foreach ($map as $k => $v) {
            if (is_array($v)) {
                foreach ($v as $_v) {
                    $needles[$_v] = $k;
                }
            } else if(is_string($k)) {
                $needles[$v] = $k;
            }
        }

        $last_pos = $src_len;

        while ($src_len > 0) {

            $found = false;
            $pos_min = $src_len;
            $_needle = '';
            $_v = '';
            foreach ($needles as $needle => $v) {
                $pos = mb_strpos($src, $needle);
                if (false === $pos) {
                    continue;
                }
                if ($pos_min > $pos) {
                    $pos_min = $pos;
                    $_needle = $needle;
                    $_v = $v;
                }
                $found = true;
            }
            if (!$found) {
                break;
            }
            //echo "={$_needle}/{$_v}:{$pos_min}=\n";
            $_cut_len = mb_strlen($_needle);
            $src = mb_substr($src, $pos_min + $_cut_len);
            $conv .= $_v;
            //echo ">{$src}<\n";
        }

        return $conv;
    }

    // }}}
    // {{{ getMapper()

    function &getMapper($name = 'None')
    {

        /*
         * simple and easy implementation.
         * don't need consideratioons about error handling and 
         * mapper instance memory performance, yet.
         */

        $__base_dir = dirname(__FILE__);
        $_name = strtr($name, '_', '/');
        $_filename = realpath($__base_dir . '/Translator/' . $_name . '.php');

        include_once($_filename);

        $klassname = 'Acme_BrainPhack_Translator_' . $name;

        $ret =& new $klassname();

        return $ret;
    }

    // }}}
}

/**
 * Local Variables:
 * mode: php
 * coding: iso-8859-1
 * tab-width: 4
 * c-basic-offset: 4
 * c-hanging-comment-ender-p: nil
 * indent-tabs-mode: nil
 * End:
 * vim: set expandtab tabstop=4 shiftwidth=4:
 */
