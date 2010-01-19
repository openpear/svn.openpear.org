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
require_once('PEAR/ErrorStack.php');
require_once('Acme/BrainPhack/MemoryStack.php');
require_once('Acme/BrainPhack/Interpreter.php');

class Interpreter_TestCase extends UnitTestCase
{
    // {{{ testConstructorAndSomeShortIllegalSrc()

    function testConstructorAndSomeShortIllegalSrc()
    {
        $MEMORY_STACK_SZ = 5;
        $INIT = '0';
        $bpms =& new Acme_BrainPhack_MemoryStack($MEMORY_STACK_SZ, $INIT);
        $es =& new PEAR_ErrorStack('test');

        $bpi =& new Acme_BrainPhack_Interpreter($bpms, $es);

        $this->assertIdentical($bpi->isStdinReady(), true);
        $bpi->disableStdin();
        $this->assertIdentical($bpi->isStdinReady(), false);
        $bpi->enableStdin();
        $this->assertIdentical($bpi->isStdinReady(), true);

        // empty source code
        $s = '';
        $r = $bpi->run($s);
        $this->assertIdentical($r, true);
        for ($i = 0; $i < $MEMORY_STACK_SZ; $i++) {
            $this->assertIdentical($bpms->get($i), $INIT);
        }
        $errors = $es->getErrors(true);
        $this->assertIdentical(count($errors), 0);

        // only illegal character
        $s = '*';
        $r = $bpi->run($s);
        $this->assertIdentical($r, true);
        $r = $es->pop();
        $this->assertEqual($r['code'], ACME_BRAINPHACK_EC_ILLEGAL_CHAR);
        $this->assertEqual($r['level'], ACME_BRAINPHACK_EL_INFO);
        $this->assertEqual($r['params'][0], 0);
        $this->assertEqual($r['params'][1], '*');
        $this->assertEqual($r['message'], 'Illegal Character.');
        // there're no other errors
        $errors = $es->getErrors(true);
        $this->assertIdentical(count($errors), 0);

        // only some illegal character
        $s = '12345';
        $r = $bpi->run($s);
        $this->assertIdentical($r, true);
        for ($i = 5; $i >= 1; $i--) {
            $r = $es->pop();
            $this->assertEqual($r['code'], ACME_BRAINPHACK_EC_ILLEGAL_CHAR);
            $this->assertEqual($r['level'], ACME_BRAINPHACK_EL_INFO);
            $this->assertEqual($r['params'][0], $i - 1);
            $this->assertEqual($r['params'][1], $i);
            $this->assertEqual($r['message'], 'Illegal Character.');
        }
        // there're no other errors
        $errors = $es->getErrors(true);
        $this->assertIdentical(count($errors), 0);
    }

    // }}}
    // {{{ testIncDecAndPrintOps()

    function testIncDecAndPrintOps()
    {
        $MEMORY_STACK_SZ = 5;
        $INIT = 0;
        $bpms =& new Acme_BrainPhack_MemoryStack($MEMORY_STACK_SZ, $INIT);
        $es =& new PEAR_ErrorStack('test');

        $bpi =& new Acme_BrainPhack_Interpreter($bpms, $es);

        $s = '+>++++->+++++--<-<.>.>.';
        $expected = sprintf('%c%c%c', 1, 2, 3);
        ob_start();
        $r = $bpi->run($s);
        $r = ob_get_clean();
        $this->assertIdentical($r, $expected);
        // no errors
        $errors = $es->getErrors(true);
        $this->assertIdentical(count($errors), 0);
        // check memory stack
        $this->assertIdentical($bpms->get(0), 1);
        $this->assertIdentical($bpms->get(1), 2);
        $this->assertIdentical($bpms->get(2), 3);
        $this->assertIdentical($bpms->get(3), $INIT);
        $this->assertIdentical($bpms->get(4), $INIT);

        $bpms->clear_reset();

        // include some illegal characters
        $s = '+>/++++->*+++?++--<-<.>.>.';
        $expected = sprintf('%c%c%c', 1, 2, 3);
        ob_start();
        $r = $bpi->run($s);
        $r = ob_get_clean();
        $this->assertIdentical($r, $expected);
        // no errors
        $r = $es->pop();
        $this->assertEqual($r['code'], ACME_BRAINPHACK_EC_ILLEGAL_CHAR);
        $this->assertEqual($r['level'], ACME_BRAINPHACK_EL_INFO);
        $this->assertEqual($r['params'][0], 13);
        $this->assertEqual($r['params'][1], '?');
        $r = $es->pop();
        $this->assertEqual($r['code'], ACME_BRAINPHACK_EC_ILLEGAL_CHAR);
        $this->assertEqual($r['level'], ACME_BRAINPHACK_EL_INFO);
        $this->assertEqual($r['params'][0], 9);
        $this->assertEqual($r['params'][1], '*');
        $r = $es->pop();
        $this->assertEqual($r['code'], ACME_BRAINPHACK_EC_ILLEGAL_CHAR);
        $this->assertEqual($r['level'], ACME_BRAINPHACK_EL_INFO);
        $this->assertEqual($r['params'][0], 2);
        $this->assertEqual($r['params'][1], '/');
        // there're no other errors
        $errors = $es->getErrors(true);
        $this->assertIdentical(count($errors), 0);
        // check memory stack
        $this->assertIdentical($bpms->get(0), 1);
        $this->assertIdentical($bpms->get(1), 2);
        $this->assertIdentical($bpms->get(2), 3);
        $this->assertIdentical($bpms->get(3), $INIT);
        $this->assertIdentical($bpms->get(4), $INIT);
    }

    // }}}
    // {{{ testAddressIncDecRangeError()

    function testAddressIncDecRangeError()
    {
        $MEMORY_STACK_SZ = 3;
        $INIT = 0;
        $bpms =& new Acme_BrainPhack_MemoryStack($MEMORY_STACK_SZ, $INIT);
        $es =& new PEAR_ErrorStack('test');

        $bpi =& new Acme_BrainPhack_Interpreter($bpms, $es);

        $s = '<';
        $r = $bpi->run($s);
        $this->assertIdentical($r, false);
        $r = $es->pop();
        $this->assertEqual($r['code'], ACME_BRAINPHACK_EC_PTR_DEC);
        $this->assertEqual($r['level'], ACME_BRAINPHACK_EL_ERROR);
        $this->assertEqual($r['params'][0], 0);

        // there're no other errors
        $errors = $es->getErrors(true);
        $this->assertIdentical(count($errors), 0);

        $bpms->clear_reset();

        $s = '>>>>';
        $r = $bpi->run($s);
        $this->assertIdentical($r, false);
        $r = $es->pop();
        $this->assertEqual($r['code'], ACME_BRAINPHACK_EC_PTR_INC);
        $this->assertEqual($r['level'], ACME_BRAINPHACK_EL_ERROR);
        $this->assertEqual($r['params'][0], 3);

        // there're no other errors
        $errors = $es->getErrors(true);
        $this->assertIdentical(count($errors), 0);
    }

    // }}}
    // {{{ testBracket()

    function testBracket()
    {
        $MEMORY_STACK_SZ = 5;
        $INIT = 0;
        $bpms =& new Acme_BrainPhack_MemoryStack($MEMORY_STACK_SZ, $INIT);
        $es =& new PEAR_ErrorStack('test');

        $bpi =& new Acme_BrainPhack_Interpreter($bpms, $es);

        $s = '+++[>+<-]>.';
        $expected = sprintf('%c', 3);
        ob_start();
        $r = $bpi->run($s);
        $r = ob_get_clean();
        $this->assertIdentical($r, $expected);
        // no errors
        $errors = $es->getErrors(true);
        $this->assertIdentical(count($errors), 0);
        // check memory stack
        $this->assertIdentical($bpms->get(0), $INIT);
        $this->assertIdentical($bpms->get(1), 3);
        $this->assertIdentical($bpms->get(2), $INIT);
        $this->assertIdentical($bpms->get(3), $INIT);
        $this->assertIdentical($bpms->get(4), $INIT);
    }

    // }}}
    // {{{ testBracketLackError()

    function testBracketLackError()
    {
        $MEMORY_STACK_SZ = 3;
        $INIT = 0;
        $bpms =& new Acme_BrainPhack_MemoryStack($MEMORY_STACK_SZ, $INIT);
        $es =& new PEAR_ErrorStack('test');

        $bpi =& new Acme_BrainPhack_Interpreter($bpms, $es);

        // #1
        $s = '[';
        $r = $bpi->run($s);
        $this->assertIdentical($r, false);
        $r = $es->pop();
        $this->assertEqual($r['code'], ACME_BRAINPHACK_EC_LACK_RBRACKET);
        $this->assertEqual($r['level'], ACME_BRAINPHACK_EL_ERROR);
        $this->assertEqual($r['params'][0], 0);
        // there're no other errors
        $errors = $es->getErrors(true);
        $this->assertIdentical(count($errors), 0);

        $bpms->clear_reset();

        // #2
        $s = '+]';
        $r = $bpi->run($s);
        $this->assertIdentical($r, false);
        $r = $es->pop();
        $this->assertEqual($r['code'], ACME_BRAINPHACK_EC_LACK_LBRACKET);
        $this->assertEqual($r['level'], ACME_BRAINPHACK_EL_ERROR);
        $this->assertEqual($r['params'][0], 1);
        // there're no other errors
        $errors = $es->getErrors(true);
        $this->assertIdentical(count($errors), 0);

        $bpms->clear_reset();

        // #3
        $s = '+++---[';
        $r = $bpi->run($s);
        $this->assertIdentical($r, false);
        $r = $es->pop();
        $this->assertEqual($r['code'], ACME_BRAINPHACK_EC_LACK_RBRACKET);
        $this->assertEqual($r['level'], ACME_BRAINPHACK_EL_ERROR);
        $this->assertEqual($r['params'][0], 6);
        $this->assertEqual($r['message'], "']' not found for '[' at 6");
        // there're no other errors
        $errors = $es->getErrors(true);
        $this->assertIdentical(count($errors), 0);

        $bpms->clear_reset();

        // #4
        $s = '+++]';
        $r = $bpi->run($s);
        $this->assertIdentical($r, false);
        $r = $es->pop();
        $this->assertEqual($r['code'], ACME_BRAINPHACK_EC_LACK_LBRACKET);
        $this->assertEqual($r['level'], ACME_BRAINPHACK_EL_ERROR);
        $this->assertEqual($r['params'][0], 3);
        $this->assertEqual($r['message'], "'[' not found for ']' at 3");
        // there're no other errors
        $errors = $es->getErrors(true);
        $this->assertIdentical(count($errors), 0);

    }

    // }}}
    // {{{ testHelloWorld1()

    function testHelloWorld1()
    {
        $MEMORY_STACK_SZ = 1024;
        $INIT = 0;
        $bpms =& new Acme_BrainPhack_MemoryStack($MEMORY_STACK_SZ, $INIT);
        $es =& new PEAR_ErrorStack('test');

        $bpi =& new Acme_BrainPhack_Interpreter($bpms, $es);

        // stupid BrainF*ck "Hello, World!"
        $s = 
            '++++++++[>++++++++<-]>++++++++.>'.     // 'H' = 72
            '++++++++++[>++++++++++<-]>+.>'.        // 'e' = 101
            '++++++++++[>+++++++++++<-]>--..>'.     // 'l' = 108 x 2
            '++++++++++[>+++++++++++<-]>+.>'.       // 'o' = 111
            '++++++++++[>++++<-]>++++.>'.           // ',' = 44
            '++++++++++[>+++<-]>++.>'.              // ' ' = 32
            '++++++++++[>++++++++<-]>+++++++.>'.    // 'W' = 72
            '++++++++++[>+++++++++++<-]>+.>'.       // 'o' = 111
            '++++++++++[>+++++++++++<-]>++++.>'.    // 'r' = 114
            '++++++++++[>+++++++++++<-]>--.>'.      // 'l' = 108
            '++++++++++[>++++++++++<-]>.>'.         // 'd' = 100
            '++++++++++[>+++<-]>+++.>';             // '!' = 33
        ob_start();
        $r = $bpi->run($s);
        $r = ob_get_clean();
        $this->assertIdentical($r, 'Hello, World!');
        // no errors
        $errors = $es->getErrors(true);
        $this->assertIdentical(count($errors), 0);
    }

    // }}}
    // {{{ testHelloWorld2()

    function testHelloWorld2()
    {
        $MEMORY_STACK_SZ = 1024;
        $INIT = 0;
        $bpms =& new Acme_BrainPhack_MemoryStack($MEMORY_STACK_SZ, $INIT);
        $es =& new PEAR_ErrorStack('test');

        $bpi =& new Acme_BrainPhack_Interpreter($bpms, $es);

        // from ja.wikipedia.org
        $s = 
            '+++++++++[>++++++++>+++++++++++>+++++<<<-]>.'.
            '>++.+++++++..+++.>-.------------.<++++++++.'.
            '--------.+++.------.--------.>+.';
        ob_start();
        $r = $bpi->run($s);
        $r = ob_get_clean();
        $this->assertIdentical($r, 'Hello, world!');
        // no errors
        $errors = $es->getErrors(true);
        $this->assertIdentical(count($errors), 0);
    }

    // }}}
    // {{{ testIgnoresInputWhenStdinNotReady()

    function testIgnoresInputWhenStdinNotReady()
    {
        $MEMORY_STACK_SZ = 5;
        $INIT = 0;
        $bpms =& new Acme_BrainPhack_MemoryStack($MEMORY_STACK_SZ, $INIT);
        $es =& new PEAR_ErrorStack('test');

        $bpi =& new Acme_BrainPhack_Interpreter($bpms, $es);

        $bpi->disableStdin();

        $s = '+++,+++';
        $r = $bpi->run($s);
        $this->assertIdentical($r, true);
        $this->assertIdentical($bpms->get(0), 6);
        // no errors
        $errors = $es->getErrors(true);
        $this->assertIdentical(count($errors), 0);
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
