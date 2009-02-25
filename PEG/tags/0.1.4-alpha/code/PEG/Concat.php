<?php

class PEG_Concat implements PEG_Action
{
    function process($result)
    {
        $ret = array();

        foreach ($result as $arr)
            foreach ($arr as $elt) $ret[] = $elt;
        return $ret;
    }
}
