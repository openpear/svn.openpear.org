<?php

class HatenaSyntax_DefinitionList extends PEG_Action
{
    function __construct()
    {
        parent::__construct(new PEG_Many1(new HatenaSyntax_Definition));
    }
    function process($result)
    {
        return array('type' => 'definitionlist',
                     'body' => $result);
    }
}