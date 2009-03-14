<?php

class PEG_Leaf implements PEG_IParser
{
    function __construct(Array $leaf)
    {
        $this->leaf = $leaf;
        $this->len = count($leaf);
    }
    
    function parse(PEG_IContext $context)
    {
        return $context->read($this->len) === $this->leaf ? $this->leaf : PEG::failure();
    }
}