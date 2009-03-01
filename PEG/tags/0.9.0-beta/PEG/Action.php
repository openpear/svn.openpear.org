<?php

abstract class PEG_Action implements PEG_IParser
{
    protected $parser;
    function __construct(PEG_IParser $p)
    {
        $this->parser = $p;
    }
    function parse(PEG_IContext $context)
    {
        $result = $this->parser->parse($context);
        return $result instanceof PEG_Failure ? $result : $this->process($result);
    }
    abstract protected function process($result);
}