<?php

class HatenaSyntax_Line extends PEG_Action
{
    function __construct($optional = false)
    {
        $elt = new HatenaSyntax_LineElement;
        $parser = $optional ? new PEG_Many($elt) : new PEG_Many1($elt);
        parent::__construct($parser);
    }
    function process($result)
    {
      
        $ret = array();
        foreach ($result as $elt) {
            if (!count($ret) || !is_string($elt) || !is_string(end($ret))) {
                $ret[] = $elt;
            }
            elseif (count($ret) && is_string($elt) && is_string(end($ret))) {
                $ret[count($ret) - 1] .= $elt;
            }
            else throw new Exception($elt);
        }
        return $ret;
    }
}