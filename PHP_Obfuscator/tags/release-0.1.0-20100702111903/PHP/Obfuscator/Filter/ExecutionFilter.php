<?php
require_once 'PHP/Obfuscator/Filter/Filter.php';
final class PHP_Obfuscator_Filter_ExecutionFilter implements PHP_Obfuscator_Filter_Filter
{
    private $code;
    public function __construct($code) {
        $this->code = $code;
    }
    public function getCode() {
        return $this->code;
    }
}
