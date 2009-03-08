<?php

class PhobBehavior extends PhobObject
{

    protected $_allocName;
    protected $_superclass;
    protected $_subclasses;

    function init()
    {
        parent::init();
        $this->_subclasses = array();
        return $this;
    }

    function name()
    {
        if ($this->_superclass)
            return '<subclass of ' . $this->superclass()->name() . '>';
        else
            return '<root class>';
    }

    function allSuperclasses()
    {
        $all = array();
        $cls = $this;
        while ($cls = $cls->superclass())
            $all[] = $cls;
        return $all;
    }

    function superclass()
    {
        return $this->_superclass;
    }

    function _setSuperclass($cls)
    {
        $this->_superclass = $cls;
    }

    function allSubclasses()
    {
        $all = array();
        self::_addSubclasses($this, $all);
        return $all;
    }

    private static function _addSubclasses($cls, &$all)
    {
        foreach ($cls->subclasses() as $sub)
            if (!array_key_exists($sub, $all)) {
                $all[] = $cls;
                self::_addSubclasses($sub, $all);
            }
    }

    function subclasses()
    {
        return $this->_subclasses;
    }

    function addSubclass($cls)
    {
        $this->_subclasses[] = $cls;
    }

    function removeSubclass($cls)
    {
        if (($i = array_search($cls, $htis->_subclasses)) !== null)
            unset($this->_subclasses[$i]);
    }

    function instanceBehavior()
    {
        return $this;
    }

    function isSubclassOfClass($class)
    {
        if ($this === $class)
            return true;
        else if ($this->_superclass === null)
            return false;
        else
            return $this->_superclass->isSubclassOfClass($class);
    }

    function instancesRespondToSelector($sel)
    {
        return method_exists($this->_name, $sel);
    }

    function alloc()
    {
        if ($this->_allocName)
            return new $this->_allocName($this);
        else {
            if (class_exists('__' . $this->_name . '__'))
                $this->_allocName = '__' . $this->_name . '__';
            else
                $this->_allocName = $this->_name;
            return new $this->_allocName($this);
        }
    }

    function make()
    {
        return $this->alloc()->init();
    }

}

