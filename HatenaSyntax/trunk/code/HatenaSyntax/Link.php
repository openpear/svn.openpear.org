<?php

class HatenaSyntax_Link extends PEG_Choice
{
    function __construct()
    {
        parent::__construct(array(HatenaSyntax_HttpLink::getInstance()));
    }
    static function getInstance()
    {
        static $o = null;
        return $o ? $o : $o = new self;
    }
}