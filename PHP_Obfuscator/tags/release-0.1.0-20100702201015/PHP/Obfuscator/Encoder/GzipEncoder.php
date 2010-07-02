<?php
require_once 'PHP/Obfuscator/Encoder/AbstractEncoder.php';
class PHP_Obfuscator_Encoder_GzipEncoder extends PHP_Obfuscator_Encoder_AbstractEncoder
{
    public function encode() {
        return gzdeflate($this->str);
    }
    public function decode() {
        return 'gzinflate(%s)';
    }
}
