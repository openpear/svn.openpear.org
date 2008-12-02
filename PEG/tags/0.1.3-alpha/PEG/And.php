<?php

class PEG_And implements PEG_IParser
{
    protected $arr;
    function __construct(Array $arr)
    {
        foreach ($arr as $p) $this->with($p);
    }
    function with(PEG_IParser $p)
    {
        $this->arr[] = $p;
        return $this;
    }
    
    function parse(PEG_IContext $c)
    {
        $arr = $this->arr;
        if (!$arr) throw new PEG_Failure;
        for ($i = 0; $i < count($arr) - 1 ; $i++) {
            $offset = $c->tell();
            $arr[$i]->parse($c);
            $c->seek($offset);
        }
        return $arr[$i]->parse($c);
    }
}