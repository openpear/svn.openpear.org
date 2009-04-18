<?php
class Template_Pilika {
    function result($string)
    {
        return $this->compile($string);
    }
    function compile($string)
    {
        $this->prefix = 'plk';
        $string = '<html xmlns:'.$this->prefix.'="http://www.deathmarch.jp/plk">'. $string. '</html>';
        $this->xml = simplexml_load_string($string);
        foreach ($this->xml->children() as $x){
            $method = $x->attributes($this->prefix,1)->getName();
            $this->{$method}($x);
        }
        return $this->removeAux($this->xml->asXML());
    }
    function content($xml)
    {
        $this->xml->{$xml->getName()} = $this->assign["{$xml->attributes($this->prefix,1)}"];
    }
    function removeAux($string)
    {
        $string = $string;
        $string = str_replace('<?xml version="1.0"?>'."\n",'',$string);
        $string = str_replace('<html xmlns:'.$this->prefix.'="http://www.deathmarch.jp/plk">','',$string);
        $string = str_replace('</html>'."\n",'',$string);
        return $string;
    }
}