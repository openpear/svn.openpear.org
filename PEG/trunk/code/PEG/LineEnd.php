<?php

class PEG_LineEnd extends PEG_Action
{
    function __construct()
    {
        $parser = PEG::choice(PEG::token("\r\n"),
                              PEG::char("\r\n"),
                              PEG::eos());
        parent::__construct($parser);
    }

    function process($v)
    {
        return $v;
    }
    function getInstance()
    {
        static $obj = null;
        return $obj ? $obj : $obj = new self;
    }
}
    
    
