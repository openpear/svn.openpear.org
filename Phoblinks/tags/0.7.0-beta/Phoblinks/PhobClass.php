<?php

class PhobClass extends PhobClassDescription
{

    protected $_name;
    protected $_primInstBehavior;

    function initCoreClass($name, $superclass, $metaclass)
    {
        parent::init();
        $this->_name = $name;
        $this->_superclass = $superclass;
        $this->_class = $metaclass;
        $this->_primInstBehavior = $name;
        return $this;
    }

    function initWithName($name, $superclass=null, $propBuilders=null,
                          $primInstBehavior=null)
    {
        parent::init();
        $this->_name = $name;
        $this->_superclass = $superclass;

        if ($primInstBehavior)
            $this->_primInstBehavior = $primInstBehavior;
        else
            $this->_primInstBehavior = $name;

        if ($propBuilders) {
            foreach ($propBuilders as $b) {
                $prop = $b->propertyClass()->alloc()->
                    initWithPropertyBuilder($b, $this);
                $this->addProperty($prop);
            }
        }
        return $this;
    }

    function name()
    {
        return $this->_name;
    }

    function reflector()
    {
        return new ReflectionClass($this->_name);
    }

    function isClass()
    {
        return true;
    }

    function isMetaclass()
    {
        return false;
    }

    function primitiveInstanceBehavior()
    {
        return $this->_primInstBehavior;
    }

}

