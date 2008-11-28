<?php

class PEG_Context implements PEG_IContext
{
    protected $s, $i = 0, $len;
    
    function __construct($s) { 
        $this->s = $s; 
        $this->len = strlen($s);
    }
    
    function read($i = 1)
    {
        $this->i += $i;
        if ($this->eos() && $i > 0) throw new PEG_Failure();
        return substr($this->s, $this->i - $i, $i);
    }
    
    function lookahead($i = 1)
    {
        return substr($this->s, $this->i, $i);
    }
    
    /**
     * @param int $i
     * @return bool
     */
    function seek($i)
    {
        if ($this->len < $i) return false;
        $this->i = $i;
        return true;
    }
    
    /**
     * @return int
     */
    function tell()
    {
        return $this->i;
    }

    /**
     * @return bool
     */
    function eos()
    {
        return $this->len <= $this->i;
    }

}