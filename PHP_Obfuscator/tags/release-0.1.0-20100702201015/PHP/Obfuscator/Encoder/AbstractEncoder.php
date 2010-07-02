<?php
require_once 'PHP/Obfuscator/Encoder/Encoder.php';
abstract class PHP_Obfuscator_Encoder_AbstractEncoder implements PHP_Obfuscator_Encoder_Encoder
{
    protected $str;
    protected $args = array();
    public function __construct(array $args = array()) {
        $this->args = $args;
    }
    public function setStr($str) {
        $this->str = $str;
    }
}
