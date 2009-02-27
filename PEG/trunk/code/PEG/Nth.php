<?php

class PEG_At extends PEG_Action
{
    /**
     * @param int $i 配列の添え字
     * @param PEG_IParser $p
     */
    function __construct($i, PEG_IParser $p)
    {
        $this->i = $i;
        parent::__construct($p);
    }

    function process($result)
    {
        return $result[$this->i];
    }
}
