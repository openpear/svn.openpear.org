<?php

class PEG_Failure
{
    private function __construct(){ } 
    
    static function it()
    {
        static $o = null;
        return $o ? $o : $o = new self;
    }
}