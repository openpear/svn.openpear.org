<?php

class HatenaSyntax_NewLine extends PEG_Choice
{
    function __construct()
    {
        parent::__construct(array(PEG_Token::get("\r\n"),
                                  PEG_Token::get("\n"),
                                  PEG_Token::get("\r")));
    }
    static function getInstance()
    {
        static $o = null;
        return $o ? $o : $o = new self;
    }
}