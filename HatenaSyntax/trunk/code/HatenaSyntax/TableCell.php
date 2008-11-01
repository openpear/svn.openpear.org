<?php

// - TableCell := "|" "*"? LineSegment<-("|", optional=true)
class HatenaSyntax_TableCell extends PEG_Action
{
    function __construct()
    {
        $parser = new PEG_Sequence();
        $parser->with(PEG_Token::get('|'))
               ->with(new PEG_LookaheadNot(HatenaSyntax_EndOfLine::getInstance()))
               ->with(new PEG_Optional(PEG_Token::get('*')))
               ->with(new HatenaSyntax_LineSegment(PEG_Token::get('|'), true));
        parent::__construct($parser);
    }
    function process($result)
    {
        return array('header' => !!$result[2],
                     'body' => $result[3]);
    }
}