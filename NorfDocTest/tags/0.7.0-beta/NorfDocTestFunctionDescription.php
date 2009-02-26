<?php

class NorfDocTestFunctionDescription extends NorfDocTestBlockStore
{

    private $_name;
    private $_module;

    function __construct($name)
    {
        parent::__construct();
        $this->_name = $name;
    }

    function signature()
    {
        return "$this->_name()";
    }

    function name()
    {
        return $this->_name;
    }

    function module()
    {
        return $this->_module;
    }

    function setModule($module)
    {
        $this->_module = $module;
    }

    function reflector()
    {
        return new ReflectionFunction($this->_name);
    }

}
