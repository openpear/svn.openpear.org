<?php

class HatenaSyntax_LineChar extends PEG_Action
{
    function __construct()
    {
        $nl = new PEG_Choice();
        $nl->with(PEG_Token::get("\n"))->with(PEG_Token::get("\r"));
        $lookahead = new PEG_Lookahead(new PEG_Not($nl));
        $linechar = new PEG_Sequence(array($lookahead, PEG_Anything::getInstance()));
        parent::__construct($linechar);
    }
    function process($result)
    {
        return $result[1];
    }
    static function getInstance()
    {
        static $obj = null;
        return $obj ? $obj : $obj = new self;
    }
}