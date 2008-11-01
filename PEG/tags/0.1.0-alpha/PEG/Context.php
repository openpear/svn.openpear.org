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
        assert(is_uint($i));
        
        $this->i += $i;
        return substr($this->s, $this->i - $i, $i);
    }
    
    function lookahead($i = 1)
    {
        assert(is_uint($i));
        
        return substr($this->s, $this->i, $i);
    }
    
    /**
     * @param int $i
     * @return bool
     */
    function seek($i)
    {
        assert(is_uint($i));
        
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