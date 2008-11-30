<?php

class PEG_Not implements PEG_IParser
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
            $this->parser->parse($context);
        } catch (PEG_Failure $e) {
            $new_offset = $context->tell();
            $context->seek($offset);
            return $context->read($new_offset - $offset);
        }
        throw new PEG_Failure;
    }
}