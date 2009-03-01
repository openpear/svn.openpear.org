<?php

class HatenaSyntax_Node
{
    protected $type, $data = array();
    function __construct($type, $data)
    {
        $this->type = $type;
        $this->data = $data;
    }
    
    function getType()
    {
        return $this->type;
    }
    
    function getData()
    {
        return $this->data;
    }
}