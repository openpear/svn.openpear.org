<?php
require_once 'PHP/Obfuscator/Encoder/AbstractEncoder.php';
class PHP_Obfuscator_Encoder_NullEncoder extends PHP_Obfuscator_Encoder_AbstractEncoder
{
    public function encode() {
        return $this->str;
    }
    public function decode() {
        return '%s';
    }
}
