#!/usr/bin/env php
<?php
if (!isset($_SERVER['argv']) && !isset($argv)) {
    fwrite(STDERR, 'Please enable the "register_argc_argv" directive in your php.ini', PHP_EOL);
    exit(1);
} else if (!isset($argv)) {
    $argv = $_SERVER['argv'];
}

require_once 'PHP/Obfuscator/Command.php';
exit(PHP_Obfuscator_Command::main($argv));
