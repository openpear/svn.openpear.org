<?php
/**
 * @package PEG
 * @author anatoo<anatoo@nequal.jp>
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @version $Id$
 */

class PEG_Preg implements PEG_IParser 
{
    protected $pattern;
    function __construct($pattern)
    {
        $this->pattern = $pattern;
    }
    
    function parse(PEG_IContext $context)
    {
        if (preg_match($this->pattern, $context->get(), $matches, null, $context->tell())) {
            $context->seek($context->tell() + strlen($matches[0]));
            return $matches[0];
        }
        
        return PEG::failure();
    }
}