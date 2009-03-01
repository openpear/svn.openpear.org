<?php

class HatenaSyntax_Factory
{
    protected $locator;
    
    function __construct(HatenaSyntax_Locator $locator)
    {
        $this->locator = $locator;
    }
    
    protected function __get($name)
    {
        return strtolower($name) === 'locator' ? $this->locator : $this->locator->$name;
    }
    
    function createLineElement(PEG_IParser $cond_parser = null)
    {
        $locator = $this->locator;
        
        $item = PEG::choice($locator->link, $locator->footnote, $locator->lineChar); 
        $parser = is_null($cond_parser) ? $item : PEG::second(PEG::seq(PEG::lookaheadNot($cond_parser), $item));
                                       
        return $parser;
    }
    
    function createLineSegment(PEG_IParser $cond_parser, $optional = false)
    {
        $elt = $this->createLineElement($cond_parser);
        $parser = $optional ? PEG::many($elt) : PEG::many1($elt);
        return HatenaSyntax_Util::segment($parser);
    }
    
    function createNodeCreater($type, PEG_IParser $parser, Array $keys = array())
    {
        return new HatenaSyntax_NodeCreater($type, $parser, $keys);
    }
}