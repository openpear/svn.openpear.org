<?php

class PEG_Join extends PEG_Action
{
    protected $glue;
    function __construct(PEG_IParser $p, $glue = '')
    {
        $this->glue = $glue;
        parent::__construct($p);
    }
    
    function process($result)
    {
        return implode($this->glue, $this->flatten($result));
    }
    
    protected function flatten(Array $arr)
    {
        $ret = array();
        foreach ($arr as $elt) if (is_array($elt)) {
            $ret = array_merge($ret, $this->flatten($elt));
        } else {
            $ret[] = $elt;
        }
        return $ret;
    }
}