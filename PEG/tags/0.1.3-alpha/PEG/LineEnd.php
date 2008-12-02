<?php

class PEG_LineEnd extends PEG_Action
{
    function __construct()
    {
        $parser = PEG::choice(PEG::token("\r"),
                              PEG::token("\n"),
                              PEG::token("\r\n"),
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
    
    
