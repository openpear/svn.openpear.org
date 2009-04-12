<?php
/**
 * PEG_Refクラスはパーサ同士がお互いに依存しているときに使われる
 * @package PEG
 * @author anatoo<anatoo@nequal.jp>
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @version $Id$
 */

class PEG_Ref implements PEG_IParser
{
    protected $parser;
    
    function is(PEG_IParser $p)
    {
        $this->parser = $p;
    }
    
    function parse(PEG_IContext $c)
    {
        return $this->parser->parse($c);
    }
}