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
        $result = $this->parser->parse($context);
        $ret = array();
        if ($result !== null) $ret[] = $result;
        
        for (;;) {
            try {
                $offset = $context->tell();
                $result = $this->parser->parse($context);
                if ($result !== null) $ret[] = $result;
            } catch (PEG_Failure $e) {
                $context->seek($offset);
                break;
            }
        }
        return $ret;
    }
}