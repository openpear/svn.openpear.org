<?php

/**
 * 文字列の終端にヒットするパーサ。
 *
 */
class PEG_EOS implements PEG_IParser
{
    protected function __construct() { }
    function parse(PEG_IContext $c)
    {
        if ($c->eos()) return false;
        return PEG::failure();
    }
    static function getInstance()
    {
        static $obj = null;
        if (!$obj) $obj = new self;
        return $obj;
    }
}