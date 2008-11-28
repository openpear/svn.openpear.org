<?php

class PEG_Choice implements PEG_IParser
{
    protected $parsers = array();
    function __construct(Array $parsers = array())
    {
        foreach ($parsers as $parser) $this->with($parser);
    }
    function with(PEG_IParser $p)
    {
        $this->parsers[] = $p;
        return $this;
    }
    function parse(PEG_IContext $c)
    {
        $offset = $c->tell();
        foreach ($this->parsers as $p) {
            try {
                $result = $p->parse($c);
            } catch (PEG_Failure $e) {
                $c->seek($offset);
                continue;
            }
            return $result;
        }
        throw new PEG_Failure;
    }
}