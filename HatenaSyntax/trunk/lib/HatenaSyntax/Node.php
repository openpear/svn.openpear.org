<?php
/**
 * @package HatenaSyntax
 * @author anatoo<anatoo@nequal.jp>
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @version $Id$
 */

class HatenaSyntax_Node
{
    protected $type, $data = array();
    function __construct($type, $data)
    {
        $this->type = $type;
        $this->data = $data;
    }
    
    function getType()
    {
        return $this->type;
    }
    
    function getData()
    {
        return $this->data;
    }

    function at($name, $defaultVal = null)
    {
        return array_key_exists($name, $this->data) 
            ? $this->data[$name] 
            : $defaultVal;
    }

    function isTopHeader()
    {
        return $this->type === 'header' && $this->at('level') === 0;
    }
}
