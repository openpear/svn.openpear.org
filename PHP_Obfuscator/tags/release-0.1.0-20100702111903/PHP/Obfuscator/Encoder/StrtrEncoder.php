<?php
require_once 'PHP/Obfuscator/Encoder/AbstractEncoder.php';
class PHP_Obfuscator_Encoder_StrtrEncoder extends PHP_Obfuscator_Encoder_AbstractEncoder
{
    const CHRS = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/';
    private $key = null;
    public function __construct(array $args = array()) {
        parent::__construct($args);
        $this->key = str_shuffle(self::CHRS);
    }
    public function encode() {
        return strtr($this->str, $this->key, self::CHRS);
    }
    public function decode() {
        return 'strtr(%s, \'' . self::CHRS . '\', \'' . $this->key . '\')';
    }
}
