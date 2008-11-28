<?php

class PEG_Many1 implements PEG_IParser
{
    protected $parser;
    function __construct(PEG_IParser $parser)
    {
        $this->parser = $parser;
    }
    function parse(PEG_IContext $context)
    {
        $ret = array($this->parser->parse($context));
        
        for (;;) {
            try {
                $offset = $context->tell();
                $ret[] = $this->parser->parse($context);
            } catch (PEG_Failure $e) {
                $context->seek($offset);
                break;
            }
        }
        return $ret;
    }
}