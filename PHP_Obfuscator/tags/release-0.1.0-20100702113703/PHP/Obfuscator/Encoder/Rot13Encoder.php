<?php
require_once 'PHP/Obfuscator/Encoder/AbstractEncoder.php';
class PHP_Obfuscator_Encoder_Rot13Encoder extends PHP_Obfuscator_Encoder_AbstractEncoder
{
    public function encode() {
        return str_rot13($this->str);
    }
    public function decode() {
        return 'str_rot13(%s)';
    }
}
