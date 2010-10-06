#!@php_bin@
<?php
if (!isset($_SERVER['argv']) && !isset($argv)) {
    fwrite(STDERR, 'Please enable the "register_argc_argv" directive in your php.ini' . PHP_EOL);
    exit(2);
} else if (!isset($argv)) {
    $argv = $_SERVER['argv'];
}

require_once 'PHP/Obfuscator/Command.php';
try {
    PHP_Obfuscator_Command::main($argv);
} catch (Exception $e) {
    fwrite(STDERR, $e->getMessage() . PHP_EOL);
    exit(1);
}
