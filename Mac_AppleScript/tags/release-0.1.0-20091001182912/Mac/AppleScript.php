<?php
/**
 * The class for building and running AppleScript command with fluent interface.
 *
 * PHP version 5
 *
 * Copyright (c) 2009 Ryusuke SEKIYAMA, All rights reserved.
 *
 * Permission is hereby granted, free of charge, to any personobtaining a
 * copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included
 * in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
 * OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
 * THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
 * DEALINGS IN THE SOFTWARE.
 *
 * @category    Mac
 * @package     Mac_AppleScript
 * @author      Ryusuke SEKIYAMA <rsky0711@gmail.com>
 * @copyright   2009 Ryusuke SEKIYAMA
 * @license     http://www.opensource.org/licenses/mit-license.php  MIT License
 * @version     SVN: $Id$
 * @link        http://openpear.org/package/Mac_AppleScript
 */

// {{{ Mac_AppleScript

/**
 * The class for building and running AppleScript command with fluent interface.
 *
 * @category    Mac
 * @package     Mac_AppleScript
 * @author      Ryusuke SEKIYAMA <rsky0711@gmail.com>
 * @version     Release: @package_version@
 * @link        http://openpear.org/package/Mac_AppleScript
 */
class Mac_AppleScript
{
    // {{{ constatns

    const OSASCRIPT_PATH = '/usr/bin/osascript';
    const RECURSION_LIMIT = 5;
    const APPEND_BREAK = 1;
    const PREPEND_BREAK = 2;

    // }}}
    // {{{ properties

    private $_command;
    private $_lastCommand;

    // }}}
    // {{{ __construct()

    /**
     * Constructor.
     *
     * @param string $command
     */
    public function __construct($command = '')
    {
        $this->_command = $command;
        $this->_lastCommand = '';
    }

    // }}}
    // {{{ __call()

    /**
     * __call()
     *
     * @param string $method
     * @param array $args
     * @return Mac_AppleScript
     */
    public function __call($method, array $args)
    {
        $command = '';
        $option = 0;
        $execute = false;

        $words = preg_split('/([A-Z][a-z0-9]*)/', $method, -1,
                            PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
        foreach ($words as $word) {
            $command .= ' ' . strtolower($word);
        }

        $argc = count($args);
        if ($argc > 0) {
            $value = $args[0];
            if ($value !== null) {
                $command .= ' ' . self::_convert($value);
            }
            if ($argc > 1) {
                $option = $args[1];
                if ($argc > 2) {
                    $execute = $args[2];
                }
            }
        }

        switch ($option) {
            case self::APPEND_BREAK:
                $this->_command .= $command . "\n";
                break;
            case self::PREPEND_BREAK:
                $this->_command .= "\n" . $command;
                break;
            default:
                $this->_command .= $command;
        }

        if ($execute) {
            $this->_lastCommand = $this->_command;
            // @todo throw an exception on failure
            fwrite(popen(self::OSASCRIPT_PATH, 'w'), $this->_command);
            $this->_command = '';
        }

        return $this;
    }

    // }}}
    // {{{ __toString()

    /**
     * __toString()
     *
     * @param void
     * @return string
     */
    public function __toString()
    {
        if ($this->_command === '') {
            return $this->_lastCommand;
        } else {
            return $this->_command;
        }
    }

    // }}}
    // {{{ _convert()

    /**
     * Converts a PHP value to an AppleScript value expression.
     *
     * @param mixed $value
     * @param int $recusion
     * @return string
     */
    static private function _convert($value, $recusion = 0)
    {
        if ($recusion > self::RECURSION_LIMIT) {
            // @todo throw an exception
            return '';

        } elseif (is_object($value)) {
            $props = get_object_vars($value);
            if (count($props) == 0) {
                return '{}';
            }

            $list = '';
            foreach ($props as $k => $v) {
                $list .= $k . ':';
                $list .= self::_convert($v, $recusion + 1);
                $list .= ', ';
            }
            return '{' . substr($list, 0, -2) . '}';

        } elseif (is_array($value)) {
            if (count($value) == 0) {
                return '{}';
            }

            $list = '';
            foreach ($value as $v) {
                $list .= self::_convert($v, $recusion + 1);
                $list .= ', ';
            }
            return '{' . substr($list, 0, -2) . '}';

        } elseif (is_int($value) || is_float($value)) {
            return (string)$value;

        } elseif (is_bool($value)) {
            return ($value) ? 'true' : 'false';

        } else {
            return '"' . str_replace('"', '\\"', (string)$value) . '"';
        }
    }

    // }}}
}

// }}}

/*
 * Local Variables:
 * mode: php
 * coding: iso-8859-1
 * tab-width: 4
 * c-basic-offset: 4
 * c-hanging-comment-ender-p: nil
 * indent-tabs-mode: nil
 * End:
 */
