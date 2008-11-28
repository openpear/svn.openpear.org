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
        return $this->process($this->parser->parse($context));
    }
    abstract protected function process($result);
}