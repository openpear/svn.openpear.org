<?php

class PEG_Many implements PEG_IParser
{
    protected $parser;
    function __construct(PEG_IParser $p)
    {
        $this->parser = $p;
    }
    function parse(PEG_IContext $context)
    {
        $ret = array();
        do {
            $offset = $context->tell();
            try {
                $result = $this->parser->parse($context);
                if ($result !== null) $ret[] = $result;
            } catch (PEG_Failure $e) {
                $context->seek($offset);
                return $ret;
            }
        } while(!$context->eos());
        return $ret;
    }
}   