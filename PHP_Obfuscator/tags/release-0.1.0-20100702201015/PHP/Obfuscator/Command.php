<?php
require_once 'PHP/Obfuscator.php';
require_once 'PHP/Obfuscator/CommandLineOptions.php';
class PHP_Obfuscator_Command
{
    public static function main(array $argv) {
        $options = new PHP_Obfuscator_CommandLineOptions($argv);
        $obfuscator = new PHP_Obfuscator();
        $obfuscator->execute(
            $options->getFileName(),
            $options->getEncoders(),
            $options->getFilters(),
            $options->isVerbose());
    }
}
