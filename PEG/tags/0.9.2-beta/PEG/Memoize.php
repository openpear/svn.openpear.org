<?php
/**
 * @package PEG
 * @author anatoo<anatoo@nequal.jp>
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @version $Id$
 */

class PEG_Memoize implements PEG_IParser
{
    protected $parser, $cache = array();
    
    function __construct(PEG_IParser $p)
    {
        $this->parser = $p;
    }
    
    function parse(PEG_IContext $context)
    {
        return $this->hit($context) ? $this->pull($context) : $this->cache($context);
    }
    
    protected function hit($context)
    {
        return isset($this->cache[spl_object_hash($context)][$context->tell()]);
    }
    
    protected function pull($context)
    {
        list($result, $newoffset) = $this->cache[spl_object_hash($context)][$context->tell()];
        $context->seek($newoffset);
        return $result;
    }
    
    protected function cache($context)
    {
        $this->cache[spl_object_hash($context)][$context->tell()] = 
            array($result = $this->parser->parse($context), $context->tell());
        return $result;
    }
}