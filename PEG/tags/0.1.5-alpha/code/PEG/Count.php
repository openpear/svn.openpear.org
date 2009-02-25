<?php

class PEG_Count extends PEG_Action
{
    function __construct(PEG_IParser $p)
    {
        parent::__construct($p);
    }
    function process($result)
    {
        return count($result);
    }
}