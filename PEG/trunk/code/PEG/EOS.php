<?php

/**
 * 文字列の終端にヒットするパーサ。
 *
 */
class PEG_EOS implements PEG_IParser
{
    function parse(PEG_IContext $c)
    {
        if ($c->eos()) return false;
        return PEG::failure();
    }
}