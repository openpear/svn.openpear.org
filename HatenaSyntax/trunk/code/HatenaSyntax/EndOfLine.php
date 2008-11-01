<?php

class HatenaSyntax_EndOfLine extends PEG_Action
{
    function __construct()
    {
        $p = new PEG_Choice(array(HatenaSyntax_NewLine::getInstance(),
                                  PEG_EOS::getInstance()));
        parent::__construct($p);  
    }
    function process($result)
    {
        if ($result === false) $result = '';
        return $result;
    }
    static function getInstance()
    {
        static $obj = null;
        return $obj ? $obj : $obj = new self;
    }
}
