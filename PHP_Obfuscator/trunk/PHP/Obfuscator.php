<?php
require_once 'PHP/Obfuscator/Encoder/EncoderChain.php';
require_once 'PHP/Obfuscator/Filter/FilterChain.php';
require_once 'PHP/Obfuscator/Filter/ExpireRestrictionFilter.php';

class PHP_Obfuscator
{
    public function execute($file_name, array $encoders, array $filters, $verbose = false) {
        if (!is_readable($file_name)) {
            throw new Exception("file {$file_name} is not readable");
        }

        $chain = new PHP_Obfuscator_Encoder_EncoderChain();
        foreach ($encoders as $encoder) {
            $class_name = "PHP_Obfuscator_Encoder_{$encoder}Encoder";
            $this->loadClass($class_name);
            $chain->add(new $class_name());
        }

        $filter = new PHP_Obfuscator_Filter_FilterChain(file_get_contents($file_name), $chain);
        foreach ($filters as $filter) {
            $class_name = "PHP_Obfuscator_Filter_{$filter}Filter";
            $this->loadClass($class_name);
            $filter->add(new $class_name());
        }
        echo $filter->process();
    }

    private function loadClass($class_name) {
        $class_file = str_replace('_', '/', $class_name) . '.php';
        if (!is_readable($class_file)) {
            throw new Exception("class file {$class_file} is not readable");
        }
        include_once $class_file;
        if (!class_exists($class_name)) {
            throw new Exception("class {$class_name} does not exist");
        }
    }
}
