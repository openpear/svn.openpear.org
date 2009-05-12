<?php
/**
 * @package PEG
 * @author anatoo<anatoo@nequal.jp>
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @version $Id$
 */

/**
 * PEG_IContextの実装クラス
 * 配列をパースするためのもの
 */
class PEG_ArrayContext implements PEG_IContext
{
    protected $arr, $i = 0, $len, $cache;
    
    /**
     *
     * @param Array $arr 配列
     */
    function __construct(Array $arr) { 
        $this->arr = array_values($arr); 
        $this->len = count($arr);
        $this->cache = new PEG_Cache;
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
    
    function readElement()
    {
        list($elt) = $this->read(1);
        return $elt;
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

    
    function save(PEG_IParser $parser, $start, $end, $val)
    {
        $this->cache->save($parser, $start, $end, $val);
    }
    
    function cache(PEG_IParser $parser)
    {
        return $this->cache->cache($parser, $this->tell());
    }
}