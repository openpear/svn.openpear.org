<?php
require_once 'PHP/Obfuscator/Encoder/EncoderChain.php';
require_once 'PHP/Obfuscator/Filter/FilterChain.php';
require_once 'PHP/Obfuscator/Filter/ExpireRestrictionFilter.php';

$chain = new PHP_Obfuscator_Encoder_EncoderChain();
if ($argc > 2) {
    foreach (array_slice($argv, 2) as $filter) {
        $class_name = "PHP_Obfuscator_Encoder_{$filter}Encoder";
        $class_file = "PHP/Obfuscator/Encoder/{$filter}Encoder.php";
        if (!is_readable($class_file)) {
            throw new Exception("class file {$class_file} is not readable");
        }
        include_once $class_file;
        if (!class_exists($class_name)) {
            throw new Exception("class {$class_name} does not exist");
        }
        $chain->add(new $class_name());
    }
}

$filter = new PHP_Obfuscator_Filter_FilterChain(file_get_contents($argv[1]), $chain);
$filter->add(new PHP_Obfuscator_Filter_ExpireRestrictionFilter(new DateTime('2010/06/28 17:45:30')));
echo $filter->process();
