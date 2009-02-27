<?php

class PEG_At extends PEG_Action
{
    protected $key;
    
    /**
     * @param int $key 配列の添え字
     * @param PEG_IParser $p
     */
    function __construct($key, PEG_IParser $p)
    {
        $this->key = $key;
        parent::__construct($p);
    }

    function process($result)
    {
        return $result[$this->key];
    }
}
