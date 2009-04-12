<?php

/**
 * PEG_IContextの実装クラス
 * 配列をパースするためのもの
 */
class PEG_ArrayContext implements PEG_IContext
{
    protected $arr, $i = 0, $len;
    
    /**
     *
     * @param Array $arr 配列
     */
    function __construct(Array $arr) { 
        $this->arr = array_values($arr); 
        $this->len = count($arr);
    }

    /**
     * @param int $i
     * @return Array
     */
    function read($i)
    {
        if ($this->eos() && $i > 0) return false;
        $this->i += $i;
        return array_slice($this->arr, $this->i - $i, $i);
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
    
    /**
     * @return array
     */
    function get()
    {
        return $this->arr;
    }

}