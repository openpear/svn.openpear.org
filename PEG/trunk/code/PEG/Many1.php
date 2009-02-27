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
        
        if ($result instanceof PEG_Failure) {
            return $result;
        } elseif (!is_null($result)) $ret = array($result);
        
        while (!$context->eos()) {
            $offset = $context->tell();
            $result = $this->parser->parse($context);
            if ($result instanceof PEG_Failure) {
                $context->seek($offset);
                break;
            }
            elseif (!is_null($result)) $ret[] = $result;
        }
        return $ret;
    }
}