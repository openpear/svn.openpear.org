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
 * @since      File available since Release 0.1.0
 */

/**
 * DocTest Sample Class
 *
 * #test __noop
 * <code>
 * private $obj;
 * private $a;
 * private $b;
 *
 * private function init()
 * {
 *     $this->a = 10;
 *     $this->b = 5;
 * }
 * </code>
 * #test __setUp
 * <code>
 * $this->obj = new #class;
 * </code>
 * #test __tearDown
 * <code>
 * $this->obj = null;
 * </code>
 *
 * @category   Testing
 * @package    Maple4_DocTest
 * @author     TAKAHASHI Kunihiko <kunit@maple-project.com>
 * @copyright  2008 Maple Project
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @since      Class available since Release 0.1.0
 */
class Test2
{
    /**
     * Add Method
     *
     * #test add
     * <code>
     * $this->init();
     * $this->assertEquals(15, $this->obj->add($this->a, $this->b));
     * </code>
     * #test add
     * <code>
     * $this->assertEquals(10, $this->obj->add(8, 2));
     * </code>
     *
     * @param integer $a
     * @param integer $b
     * @return integer
     * @access public
     */
    public function add($a, $b)
    {
        return $a + $b;
    }

    /**
     * Sub Method
     *
     * #test
     * <code>
     * $this->init();
     * #eq(5, #f($this->a, $this->b));
     * </code>
     * #test
     * <code>
     * #eq(6, #f(8, 2));
     * </code>
     * #test sub2
     * <code>
     * #eq(5, #f(8, 3));
     * </code>
     * #test sub2
     * <code>
     * #eq(4, #f(8, 4));
     * </code>
     *
     * @param integer $a
     * @param integer $b
     * @return integer
     * @access public
     */
    public function sub($a, $b)
    {
        return $a - $b;
    }

    /**
     * Mul Method
     *
     * #test __noop
     * <code>
     * public function testMul()
     * {
     *     $this->init();
     *     $this->assertEquals(50, $this->obj->mul($this->a, $this->b));
     * }
     * </code>
     * #test sub2
     * <code>
     * #eq(40, #f(8, 5));
     * </code>
     * #test __noop
     * <code>
     * public function testMul2()
     * {
     *     #eq(48, #f(8, 6));
     * }
     * </code>
     *
     * @param integer $a
     * @param integer $b
     * @return integer
     * @access public
     */
    public function mul($a, $b)
    {
        return $a * $b;
    }
}
