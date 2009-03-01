<?php

/**
 * どのような文字にでもヒットするパーサ
 *
 */
class PEG_Anything implements PEG_IParser
{
    function __construct() { }
    function parse(PEG_IContext $context)
    {
        if ($context->eos()) throw new PEG_Failure;
        return $context->read(1);
    }
}