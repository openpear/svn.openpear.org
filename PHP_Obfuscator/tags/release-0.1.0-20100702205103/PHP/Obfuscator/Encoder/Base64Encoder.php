<?php
require_once 'PHP/Obfuscator/Encoder/AbstractEncoder.php';
class PHP_Obfuscator_Encoder_Base64Encoder extends PHP_Obfuscator_Encoder_AbstractEncoder
{
    public function encode() {
        return str_replace('=', '', base64_encode($this->str));
    }
    public function decode() {
        return 'base64_decode(%s)';
    }
}
