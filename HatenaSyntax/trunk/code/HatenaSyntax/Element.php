<?php

// - Element := Header | DefinitionList | Table | List | 
//              BlockQuote | Pre | SuperPre | Line<-(optional=true)
class HatenaSyntax_Element implements PEG_IParser
{
    protected $p;
    function __construct()
    {
        $blockquote = HatenaSyntax_BlockQuote::getInstance();
        $blockquote->setElement($this);
        $line = new PEG_Sequence(array(new HatenaSyntax_Line(true), HatenaSyntax_NewLine::getInstance()));
        $parser = new PEG_Choice();
        $parser->with(new HatenaSyntax_Header)
               ->with($blockquote)
               ->with(new HatenaSyntax_DefinitionList)
               ->with(new HatenaSyntax_Table)
               ->with(new HatenaSyntax_List)
               ->with(HatenaSyntax_Pre::getInstance())
               ->with(HatenaSyntax_SuperPre::getInstance())
               ->with(new PEG_CallbackAction(array($this, 'processLine'), $line));
        $this->p = $parser;
    }
    function processLine($result)
    {
        return array('type' => 'line', 'body' => $result[0]);
    }
    function parse(PEG_IContext $c)
    {
        return $this->p->parse($c);
    }
    function processNewLine($result)
    {
        return array('type' => 'empty_line');
    }
}