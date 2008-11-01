<?php

class HatenaSyntax_Definition extends PEG_Action
{
    function __construct()
    {
        $eol = HatenaSyntax_EndOfLine::getInstance();
        
        $content = new HatenaSyntax_LineSegment(PEG_Token::get(':'));
        $parser = new PEG_Sequence();
        $parser->with(PEG_Token::get(':'))
               ->with($content)
               ->with(PEG_Token::get(':'))
               ->with($content)
               ->with($eol);
        parent::__construct($parser);
    }
    function process($result)
    {
        return array($result[1], $result[3]);
    }
}