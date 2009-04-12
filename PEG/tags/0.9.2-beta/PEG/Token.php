<?php
/**
 * @package PEG
 * @author anatoo<anatoo@nequal.jp>
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @version $Id$
 */

class PEG_Token implements PEG_IParser
{
    protected $str;
    protected function __construct($str)
    {
        $this->str = $str;
    }
    function parse(PEG_IContext $c)
    {
        if ($c->read(strlen($this->str)) === $this->str)
            return $this->str;
        else 
            return PEG::failure();
    }
    static function get($token)
    {
        static $dict = array();
        return isset($dict[$token]) ? $dict[$token] : $dict[$token] = new self($token);
    }
}