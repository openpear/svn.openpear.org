<?php

class PEG_Optional implements PEG_IParser
{
    protected $parser;
    function __construct(PEG_IParser $p)
    {
        $this->parser = $p;
    }
    function parse(PEG_IContext $context)
    {
        $offset = $context->tell();
        try {
            $result = $this->parser->parse($context);
            return $result;
        } catch (PEG_Failure $e) {
            $context->seek($offset);
            return false;
        }
    }
}