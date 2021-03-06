#!@php_bin@
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

require_once('Console/GetOpt.php');
require_once('PEAR/ErrorStack.php');
require_once('Acme/BrainPhack/Translator.php');
require_once('Acme/BrainPhack/MemoryStack.php');
require_once('Acme/BrainPhack/Interpreter.php');

function usage()
{
    $_this = basename(__FILE__);
    echo "usage: {$_this} [options] filename\n";
    echo "\tfilename: you can give only 1 filename.\n";
    echo "\t\tmultiple filenames invokes error and show this help.\n";
    echo "\toptions:\n";
    echo "\t-b : convert to BrainF*ck code and display it(not executed).\n";
    echo "\t-h : show this help.\n";
    echo "\t-m size : memory stack size by KB (default is 32).\n";
    echo "\t-t translator : BrainF*ck translator.\n";
    echo "\t\tex: Acme/BrainPhack/Translator/Foo.php : \"-t Foo\"\n";
    echo "\t\tex: Acme/BrainPhack/Translator/Bar/Baz.php : \"-t Bar_Baz\"\n";
    echo "\t\t(default: Acme_BrainPhack_Translator_None.php)\n";
}

$cg =& new Console_GetOpt();
$argv = $cg->readPHPArgv();
array_shift($argv);

$params = $cg->getopt2($argv, 'bhm:t:');
if (PEAR::isError($params)) {
    echo 'Error: ' . $params->getMessage() . "\n";
    usage();
    exit(1);
}
list($options, $remains) = $params;

$settings = array(
    'convert_not_run' => false,
    'stack_size' => 32,
    'translator' => 'None',
    );

foreach ($options as $option) {
    list($k, $v) = $option;
    switch ($k) {
    case 'b':
        $settings['convert_not_run'] = true;
        break;
    case 'h':
        usage();
        exit(0);
    case 'm':
        $_v = intval($v);
        if (0 == $_v) {
            echo "Error: memory stack size (-m) is zero.\n";
            usage();
            exit(1);
        }
        $settings['stack_size'] = $_v;
        break;
    case 't':
        $settings['translator'] = $v;
        break;
    default:
    }
}

if (1 != count($remains)) {
    echo "Error: filename is not given, or, multiple files are given.\n";
    usage();
    exit(1);
}

$src = file_get_contents($remains[0]);
if (false === $src) {
    echo "Error: can't read from '{$remains[0]}'.\n";
    exit(1);
}

$bpt =& new Acme_BrainPhack_Translator();
$bptm =& $bpt->getMapper($settings['translator']);
$map = $bptm->getMap();
$bpsrc = $bpt->translate($map, $src);

if (true === $settings['convert_not_run']) {
    echo $bpsrc;
    echo "\n";
    exit(0);
}

$sz = 1024 * $settings['stack_size'];
$bpms =& new Acme_BrainPhack_MemoryStack($sz, 0);
$errorStack =& new PEAR_ErrorStack('Acme_BrainPhack CUI ErrorStack');
$bpi =& new Acme_BrainPhack_Interpreter($bpms, $errorStack);
$bpi->run($bpsrc);
$errors = $errorStack->getErrors(true);
foreach ($errors as $e) {
    $_l = $e['level'];
    $_m = $e['message'];
    $_c = $e['code'];
    $_p = '(' . implode(', ', $e['params']) . ')';
    echo "{$_l} {$_m} {$_p} (code={$_c})\n";
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
 * vim: set expandtab tabstop=4 shiftwidth=4 filetype=php:
 */
