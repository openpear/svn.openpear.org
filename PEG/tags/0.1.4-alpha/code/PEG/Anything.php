<?php

/**
 * どのような文字列にでもヒットするパーサ
 *
 */
class PEG_Anything implements PEG_IParser
{
    protected function __construct() { }
    function parse(PEG_IContext $context)
    {
        if ($context->eos()) throw new PEG_Failure;
        return $context->read(1);
    }
    static function getInstance()
    {
        static $obj = null;
        if (!$obj) $obj = new self;
        return $obj;
    }
}