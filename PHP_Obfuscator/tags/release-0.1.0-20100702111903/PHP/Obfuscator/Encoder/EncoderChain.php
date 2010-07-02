<?php
require_once 'PHP/Obfuscator/Encoder/Encoder.php';
class PHP_Obfuscator_Encoder_EncoderChain
{
    private $chain = array();
    public function add(PHP_Obfuscator_Encoder_Encoder $filter) {
        $this->chain[] = $filter;
        return $this;
    }
    public function encode($code = 'fread($__f, %d)') {
        foreach ($this->chain as $filter) {
            $filter->setStr($code);
            $code = $filter->encode();
        }
        return $code;
    }
    public function decode($code = 'fread($__f, %d)') {
        $chain = $this->chain;
        krsort($chain);
        foreach ($chain as $filter) {
            $code = sprintf($filter->decode(), $code);
        }
        return "{$code}";
    }
}
