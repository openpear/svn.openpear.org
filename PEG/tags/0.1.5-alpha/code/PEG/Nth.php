<?php

class PEG_Nth implements PEG_IParser
{
    /**
     * @param int $i 配列の添え字
     * @param PEG_IParser $p
     */
    function __construct($i, PEG_IParser $p)
    {
        $this->i = $i;
        $this->p = $p;
    }

    function parse(PEG_IContext $context)
    {
        $result = $this->p->parse($context);
        return $result[$this->i];
    }
}
