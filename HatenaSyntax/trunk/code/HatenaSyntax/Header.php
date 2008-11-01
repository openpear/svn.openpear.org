<?php

class HatenaSyntax_Header extends PEG_Action
{
    function __construct()
    {
        $lc = HatenaSyntax_LineChar::getInstance();
        $fn = HatenaSyntax_Footnote::getInstance();
        $parser = new PEG_Sequence();
        $parser->with(new PEG_Many1(PEG_Token::get('*')))
               ->with(new PEG_Many(new PEG_Choice(array($fn, $lc))))
               ->with(HatenaSyntax_EndOfLine::getInstance());
        parent::__construct($parser);
    }
    protected function process($result)
    {        
        $body = array();
        foreach ($result[1] as $elt) {
            if (count($body) && is_string($elt) && is_string(end($body))) {
                $body[count($body) - 1] .= $elt;
            }
            else {
                $body[] = $elt;
            }
        }
        return array('type' => 'header',
                     'level' => count($result[0]), 
                     'body' => $body);
    }
}