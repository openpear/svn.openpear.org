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
        for(;;) {
            $offset = $context->tell();
            try {
                $ret[] = $this->parser->parse($context);
            } catch (PEG_Failure $e) {
                $context->seek($offset);
                return $ret;
            }
        }
    }
}   