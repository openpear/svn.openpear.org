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

    function testConstructorAndPointerIncDec()
    {
        $MEMORY_STACK_SZ = 5;
        $INIT = '0';
        $bpms =& new Acme_BrainPhack_MemoryStack($MEMORY_STACK_SZ, $INIT);
        $es =& new PEAR_ErrorStack('Acme_BrainPhack');

        $bpi =& new Acme_BrainPhack_Interpreter($bpms, $es);

        $this->assertIdentical(true, $bpi->isStdinReady());
        $bpi->disableStdin();
        $this->assertIdentical(false, $bpi->isStdinReady());
        $bpi->enableStdin();
        $this->assertIdentical(true, $bpi->isStdinReady());
/*
        $s = '';
        $r = $bpi->run($s);
        $this->assertIdentical(true, $r);
        for ($i = 0; $i < $MEMORY_STACK_SZ; $i++) {
            $this->assertIdentical($INIT, $bpms->get($i));
        }

        $bpi =& new Acme_BrainPhack_Interpreter($bpms, $es);
        $s = '*';
        $r = $bpi->run($s);
        $this->assertIdentical(false, $r);
        $this->assertIdentical(1, count($es->getErrors()));
        $e = $es->pop();

        $bpi =& new Acme_BrainPhack_Interpreter($bpms, $es);
        $s = '+>++++->+++++--<-';
        $r = $bpi->run($s);
        $this->assertIdentical(false, $r);
        $this->assertIdentical(1, count($es->getErrors()));
        $e = $es->pop();
 */
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
