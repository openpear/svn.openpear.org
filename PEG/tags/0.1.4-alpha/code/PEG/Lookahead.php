<?php

class PEG_Lookahead implements PEG_IParser
{
    protected $parser;
    function __construct(PEG_IParser $p)
    {
        $this->parser = $p;
    }
    function parse(PEG_IContext $context)
    {
        $offset = $context->tell();
        $this->parser->parse($context);
        $context->seek($offset);
        return false;
    }
}