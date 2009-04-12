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
}