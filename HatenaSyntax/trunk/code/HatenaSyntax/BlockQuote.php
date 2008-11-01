<?php

// - BlockQuote := ">>" NewLine Element* "<<" EndOfLine
class HatenaSyntax_BlockQuote implements PEG_IParser
{
    protected $p;
    function setElement(HatenaSyntax_Element $elt)
    {
        $elt = new PEG_Sequence(array(new PEG_LookaheadNot(new PEG_Sequence(array(PEG_Token::get('<<'), HatenaSyntax_EndOfLine::getInstance()))), $elt));
        $this->p = new PEG_Sequence();
        $this->p->with(PEG_Token::get('>>'))
                ->with(HatenaSyntax_NewLine::getInstance())
                ->with(new PEG_Many($elt))
                ->with(PEG_Token::get('<<'))
                ->with(HatenaSyntax_EndOfLine::getInstance());
    }
    function parse(PEG_IContext $context)
    {
        $result = $this->p->parse($context);
        $ret = array();
        foreach ($result[2] as $elt) {
            $ret[] = $elt[1];
        }
        
        return array('type' => 'blockquote',
                     'body' => $ret);
    }
    static function getInstance()
    {
        static $o = null;
        return $o ? $o : $o = new self;
    }
}