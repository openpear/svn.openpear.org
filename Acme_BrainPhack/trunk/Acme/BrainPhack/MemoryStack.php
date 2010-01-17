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
class Acme_BrainPhack_MemoryStack
{
    // {{{ properties

    /**
     * maximum stack size
     *
     * @var integer
     * @access protected
     */
    var $_stack_size = 0;

    /**
     * memory stack
     *
     * @var array
     * @access protected
     */
    var $_stack;

    /**
     * memory pointer address
     *
     * @var integer
     * @access protected
     */
    var $_curr_ptr = 0;

    // }}}
    // {{{ constructor

    /**
     * Constructor
     *
     * @access public
     * @param integer maximum stack size
     * @param mixed initial memory value (optional)
     *              If omitted, memory stack is initialized by 0(zero).
     */
    function Acme_BrainPhack_MemoryStack($sz, $init = 0)
    {
        $this->_stack = array();
        $this->_stack_size = $sz;
        for ($i = 0; $i < $sz; $i++) {
            $this->_stack[$i] = $init;
        }
    }

    // }}}
    // {{{ memory value getter/setter

    /**
     * return memory value
     *
     * @access public
     * @param integer memory address(optional)
     *                If omitted, current pointer address is used.
     * @return mixed pointed memory value.
     *  If memory address < 0 or maximum stack size < memory address, 
     *  return false.
     */
    function get($i = null)
    {
        if (is_null($i)) {
            return $this->_stack[$this->_curr_ptr];
        }
        if (0 > $i) {
            return false;
        }
        if ($i >= $this->_stack_size) {
            return false;
        }
        return $this->_stack[$i];
    }

    /**
     * set new memory value
     *
     * @access public
     * @param mixed new value
     * @param integer memory address(optional)
     *                If omitted, current pointer address is used.
     * @return mixed pointed old memory value.
     *  If memory address < 0 or maximum stack size < memory address, 
     *  return false and nothing changes.
     */
    function set($v, $i = null)
    {
        if (is_null($i)) {
            $old = $this->_stack[$this->_curr_ptr];
            $this->_stack[$this->_curr_ptr] = $v;
            return $old;
        }
        if (0 > $i) {
            return false;
        }
        if ($i >= $this->_stack_size) {
            return false;
        }
        $old = $this->_stack[$i];
        $this->_stack[$i] = $v;
        return $old;
    }

    // }}}
    // {{{ pointer address increment/decrement

    /**
     * increment pointer address
     *
     * @access public
     * @return array array('old pointer address', 'new pointer address')
     *  If incremented memory address WILL BE lesser than < 0 or 
     *  oversized maximum stack size, memory address doesn't be incremented
     *  (nothing changed), and return false
     */
    function ptr_inc()
    {
        $old = $this->_curr_ptr;
        if ($this->_curr_ptr == $this->_stack_size) {
            return false;
        }
        $this->_curr_ptr++;
        return array($old, $this->_curr_ptr);
    }

    /**
     * decrement pointer address
     *
     * @access public
     * @return array array('old pointer address', 'new pointer address')
     *  If decremented memory address WILL BE lesser than < 0 or 
     *  oversized maximum stack size, memory address doesn't be incremented
     *  (nothing changed), and return false
     */
    function ptr_dec()
    {
        $old = $this->_curr_ptr;
        if (0 == $this->_curr_ptr) {
            return false;
        }
        $this->_curr_ptr--;
        return array($old, $this->_curr_ptr);
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
