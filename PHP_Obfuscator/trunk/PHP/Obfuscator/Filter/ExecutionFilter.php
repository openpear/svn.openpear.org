<?php
require_once 'PHP/Obfuscator/Filter/Filter.php';
final class PHP_Obfuscator_Filter_ExecutionFilter implements PHP_Obfuscator_Filter_Filter
{
    private $code;
    public function setArgs(array $code) {
        $this->code = $code[0];
    }
    public function getCode() {
        return $this->code;
    }
}
