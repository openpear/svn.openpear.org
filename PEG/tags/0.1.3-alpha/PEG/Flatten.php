<?php

class PEG_Flatten extends PEG_Action
{
    function process($v)
    {
        $ret = array();
        foreach ($v as $elt) {
            if (is_array($elt)) $ret = array_merge($ret, $this->process($elt));
        }
        return $ret;
    }
}
