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
 * BrainF*ck MemoryStack
 *
 * @author msakamoto-sf <sakamoto-gsyc-3s@glamenv-septzen.net>
 * @package Acme_BrainPhack
 * @since 0.0.1
 */

define('ACME_BRAINPHACK_OP_PTR_INC', '>');
define('ACME_BRAINPHACK_OP_PTR_DEC', '<');
define('ACME_BRAINPHACK_OP_VAL_INC', '+');
define('ACME_BRAINPHACK_OP_VAL_DEC', '-');
define('ACME_BRAINPHACK_OP_PRINT', '.');
define('ACME_BRAINPHACK_OP_INPUT', ',');
define('ACME_BRAINPHACK_OP_JMP_Z', '[');
define('ACME_BRAINPHACK_OP_JMP_NZ', ']');

class Acme_BrainPhack_Interpreter
{
    var $_memoryStack;
    var $_errorStack;
    var $_stdinReady = false;

    function Acme_BrainPhack_Interpreter(&$memoryStack, &$errorStack)
    {
        $this->_memoryStack =& $memoryStack;
        $this->_errorStack =& $errorStack;
        if ('cli' == php_sapi_name()) {
            $this->_stdinReady = true;
        }
    }

    function isStdinReady()
    {
        return $this->_stdinReady;
    }
    function enableStdin()
    {
        $this->_stdinReady = true;
    }
    function disableStdin()
    {
        $this->_stdinReady = false;
    }
    function run($src)
    {
        $src_i = 0;
        $src_sz = strlen($src);

        while ($src_i < $src_sz) {
            $op = $src[$src_i];
            $src_i++;

            switch ($op) {
            case OP_PTR_INC:
                $r = $this->_memoryStack->ptr_inc();
                if (false === $r) {
                    $this->_errorStack->push(
                        ACME_BRAINPHACK_EC_PTR_INC,
                        'Pointer Increment Error(maybe out of range).',
                        ACME_BRAINPHACK_EL_WARN,
                        array($src_i)
                    );
                    return false;
                }
                break;
            case OP_PTR_DEC:
                $r = $this->_memoryStack->ptr_dec();
                if (false === $r) {
                    $this->_errorStack->push(
                        ACME_BRAINPHACK_EC_PTR_DEC,
                        'Pointer Decrement Error(maybe minus address).',
                        ACME_BRAINPHACK_EL_WARN,
                        array($src_i)
                    );
                    return false;
                }
                break;
            case OP_VAL_INC:
                $v = $this->_memoryStack->get();
                $this->_memoryStack->set($v + 1);
                break;
            case OP_VAL_DEC:
                $v = $this->_memoryStack->get();
                $this->_memoryStack->set($v - 1);
                break;
            case OP_PRINT:
                $v = $this->_memoryStack->get();
                echo $v;
                break;
            case OP_INPUT:
                $c = fgetc(STDIN);
                if ($c === false) {
                    echo "input error.\n";
                    break;
                }
                $stack->set($c);
                break;
            case OP_JMP_Z:
                /*
                $v = $stack->get();
                if (0 == $v) { // ambiguous compares... '0' == 0, 0 == 0.
                    $bk_i = $src_i;
                    $_found = false;
                    while ($src_i < $src_sz) {
                        if (OP_JMP_NZ == $src[$src_i]) {
                            $_found = true;
                            break;
                        }
                        $src_i++;
                    }
                    if (!$_found) {
                        echo "not found pair ']' for '[' at {$bk_i}\n";
                    } else {
                        $src_i++;
                    }
                }
                 */
                break;
            case OP_JMP_NZ:
                /*
                $v = $stack->get();
                if (0 != $v) { // ambiguous compares... '0' == 0, 0 == 0.
                    $bk_i = $src_i;
                    $_found = false;
                    while (0 < $src_i) {
                        $src_i--;
                        if (OP_JMP_Z == $src[$src_i]) {
                            $_found = true;
                            break;
                        }
                    }
                    if (!$_found) {
                        echo "not found pair '[' for ']' at {$bk_i}, ignored.\n";
                        $src_i = $bk_i;
                    } else {
                        $src_i++;
                    }
                }
                 */
                break;
            default:
                $this->_errorStack->push(
                    ACME_BRAINPHACK_EC_ILLEGAL_CHAR,
                    'Illegal Character.',
                    ACME_BRAINPHACK_EL_INFO,
                    array($src_i, $op)
                );
            }
        }
        return true;
    }
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
