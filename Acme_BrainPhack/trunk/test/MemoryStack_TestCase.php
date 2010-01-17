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
 * requires
 */
require_once('Acme/BrainPhack/MemoryStack.php');

class MemoryStack_TestCase extends UnitTestCase
{

    function testConstructorAndPointerIncDec()
    {
        // create 3 sized memory stack
        $bpms =& new Acme_BrainPhack_MemoryStack(3);

        // decrement -> lesser than 0, return false
        $r = $bpms->ptr_dec();
        $this->assertIdentical(false, $r);

        list($old, $new) = $bpms->ptr_inc();
        $this->assertIdentical(0, $old);
        $this->assertIdentical(1, $new);

        list($old, $new) = $bpms->ptr_inc();
        $this->assertIdentical(1, $old);
        $this->assertIdentical(2, $new);

        list($old, $new) = $bpms->ptr_inc();
        $this->assertIdentical(2, $old);
        $this->assertIdentical(3, $new);

        // increment -> size over, return false
        $r = $bpms->ptr_inc();
        $this->assertIdentical(false, $r);

        list($old, $new) = $bpms->ptr_dec();
        $this->assertIdentical(3, $old);
        $this->assertIdentical(2, $new);

        list($old, $new) = $bpms->ptr_dec();
        $this->assertIdentical(2, $old);
        $this->assertIdentical(1, $new);

        list($old, $new) = $bpms->ptr_dec();
        $this->assertIdentical(1, $old);
        $this->assertIdentical(0, $new);
    }

    function testGetterSetterAndMemoryInitialize()
    {
        // default, initialized by 0
        $bpms =& new Acme_BrainPhack_MemoryStack(3);
        $r = $bpms->get();
        $this->assertIdentical(0, $r);
        $bpms->ptr_inc();
        $r = $bpms->get();
        $this->assertIdentical(0, $r);
        $bpms->ptr_inc();
        $r = $bpms->get();
        $this->assertIdentical(0, $r);

        // initialized by 1
        $bpms =& new Acme_BrainPhack_MemoryStack(3, 1);
        $r = $bpms->get();
        $this->assertIdentical(1, $r);
        $bpms->ptr_inc();
        $r = $bpms->get();
        $this->assertIdentical(1, $r);
        $bpms->ptr_inc();
        $r = $bpms->get();
        $this->assertIdentical(1, $r);

        // initialized by 'A'
        $bpms =& new Acme_BrainPhack_MemoryStack(3, 'A');
        $r = $bpms->get();
        $this->assertIdentical('A', $r);
        $bpms->ptr_inc();
        $r = $bpms->get();
        $this->assertIdentical('A', $r);
        $bpms->ptr_inc();
        $r = $bpms->get();
        $this->assertIdentical('A', $r);

        // setter test
        $old = $bpms->set('B');
        $this->assertIdentical('A', $r);
        $r = $bpms->get();
        $this->assertIdentical('B', $r);

        // get/set with pointer address
        $r = $bpms->get(1);
        $this->assertIdentical('A', $r);
        $old = $bpms->set('C', 1);
        $this->assertIdentical('A', $r);
        $r = $bpms->get(1);
        $this->assertIdentical('C', $r);
        $r = $bpms->get(2);
        $this->assertIdentical('B', $r);
    }

    function testGetterSetterNearBorder()
    {
        // size = 3, address range = 0 - 2.
        $bpms =& new Acme_BrainPhack_MemoryStack(3);

        // < 0
        $r = $bpms->get(-1);
        $this->assertIdentical(false, $r);
        $r = $bpms->get(-2);
        $this->assertIdentical(false, $r);
        $r = $bpms->set(100, -1);
        $this->assertIdentical(false, $r);
        $r = $bpms->set(100, -2);
        $this->assertIdentical(false, $r);

        // > 3
        $r = $bpms->get(3);
        $this->assertIdentical(false, $r);
        $r = $bpms->get(4);
        $this->assertIdentical(false, $r);
        $r = $bpms->set(100, 3);
        $this->assertIdentical(false, $r);
        $r = $bpms->set(100, 4);
        $this->assertIdentical(false, $r);


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
