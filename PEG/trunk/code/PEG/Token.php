<?php
/**
 * @package PEG
 * @author anatoo<anatoo@nequal.jp>
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @version $Id$
 */

class PEG_Token implements PEG_IParser
{
    protected $str, $caseSensitive;
    function __construct($str, $caseSensitive = true)
    {
        $this->str = $caseSensitive ? $str : strtolower($str);
        $this->caseSensitive = $caseSensitive;
    }
    function parse(PEG_IContext $c)
    {
        $str = $c->read(strlen($this->str));
        if ($this->caseSensitive && $str === $this->str)
            return $str;
        elseif (!$this->caseSensitive && strtolower($str) === $this->str)
            return $str;
        else 
            return PEG::failure();
    }
    static function get($token)
    {
        static $dict = array();
        return isset($dict[$token]) ? $dict[$token] : $dict[$token] = new self($token);
    }
}