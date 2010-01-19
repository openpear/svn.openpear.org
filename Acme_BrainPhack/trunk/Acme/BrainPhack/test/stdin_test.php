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

$include_path = ini_get('include_path');
//$__pear_src_dir = realpath(dirname(__FILE__).'/../');
$__pear_src_dir = realpath(dirname(__FILE__).'/../../'); // TODO
ini_set('include_path', $__pear_src_dir.PATH_SEPARATOR.$include_path);

require_once('PEAR/ErrorStack.php');
require_once('Acme/BrainPhack/MemoryStack.php');
require_once('Acme/BrainPhack/Interpreter.php');

// ACME_BRAINPHACK_OP_INPUT(',') manual test from php-cli

$MEMORY_STACK_SZ = 5;
$INIT = 0;
$bpms =& new Acme_BrainPhack_MemoryStack($MEMORY_STACK_SZ, $INIT);
$es =& new PEAR_ErrorStack('test');

$bpi =& new Acme_BrainPhack_Interpreter($bpms, $es);

echo "input one char:";
$s = ',.';
ob_start();
$r = $bpi->run($s);
$o = ob_get_clean();
echo "\nconfirm: your input is [" . $o . "]\n";

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
