<?php

/**
 * 選択。正規表現でいう"|"。
 *
 */
class PEG_Choice implements PEG_IParser
{
    protected $parsers = array();
    
    function __construct(Array $parsers = array())
    {
        foreach ($parsers as $parser) $this->with($parser);
    }

    protected function with(PEG_IParser $p)
    {
        $this->parsers[] = $p;
    }
    
    function parse(PEG_IContext $c)
    {
        $offset = $c->tell();
        foreach ($this->parsers as $p) {
            $result = $p->parse($c);
            
            if ($result instanceof PEG_Failure) {
                $c->seek($offset);
                continue;
            }
            else return $result;
        }
        return PEG::failure();
    }
}