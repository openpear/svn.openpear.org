<?php

class PEG_Sequence implements PEG_IParser
{
    protected $parsers = array();
    function __construct(Array $parsers = array())
    {
        foreach ($parsers as $p) $this->with($p);
    }
    function with(PEG_IParser $p)
    {
        $this->parsers[] = $p;
        return $this;
    }
    function parse(PEG_IContext $context)
    {
        $ret = array();
        foreach ($this->parsers as $parser) {
            $offset = $context->tell();
            $ret[] = $parser->parse($context);
        }
        return $ret;
    }
}