<?php

// SuperPreElement := NewLine (?! "||<" EndOfLine) ((?! "||<" EndOfLine) LineChar)* 
class HatenaSyntax_SuperPreElement extends PEG_Action
{
    function __construct()
    {
        $cond = new PEG_LookaheadNot(new PEG_Sequence(array(PEG_Token::get('||<', 
                                                            HatenaSyntax_EndOfLine::getInstance()))));
        $elt = new PEG_Sequence();
        $elt->with($cond)
            ->with(HatenaSyntax_LineChar::getInstance());
            
        $parser = new PEG_Sequence();
        $parser->with(HatenaSyntax_NewLine::getInstance())
               ->with($cond)
               ->with(new PEG_Many($elt));
        parent::__construct($parser);
    }
    function process($result)
    {
        $line = '';
        foreach ($result[2] as $elt) {
            $line .= $elt[1];
        }
        return $line;
    }
}