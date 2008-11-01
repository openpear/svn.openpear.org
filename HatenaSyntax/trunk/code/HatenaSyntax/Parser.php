<?php

class HatenaSyntax_Parser
{
    protected $p;
    function __construct()
    {
        $this->p = new PEG_Many(new HatenaSyntax_Element);
    }
    function parse($str)
    {
        try {
            $context = new PEG_Context($str);
            $result = $this->p->parse($context);
        } catch (PEG_Failure $e) {
            return false;
        }
        return $result;
    }
}