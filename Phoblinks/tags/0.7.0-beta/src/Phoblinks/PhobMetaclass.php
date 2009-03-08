<?php

class PhobMetaclass extends PhobClassDescription
{

    protected $_thisClass;

    function initWithInstanceBehaviorName($name,
                                          $superclass=null,
                                          $propBuilders=null,
                                          $primInstBehavior=null,
                                          $primClassBehavior=null)
    {
        parent::init();
        $this->createInstanceBehavior($name, $superclass, $propBuilders,
                                      $primInstBehavior,
                                      $primClassBehavior);
        return $this;
    }

    function initWithInstanceBehavior($thisClass, $superclass)
    {
        parent::init();
        $this->_name = $thisClass->name() . ' class';
        $this->_superclass = $superclass;
        $this->_thisClass = $thisClass;
        return $this;
    }

    function __toString()
    {
        return sprintf('<%s (%s)>', get_class($this),
                       $this->_thisClass ? $this->_thisClass->name() : '-');
    }

    function name()
    {
        return $this->_name;
    }

    function classBehavior()
    {
        return $this;
    }

    function instanceBehavior()
    {
        return $this->_thisClass;
    }

    function createInstanceBehavior($name, $superclass, $properties=null,
                                    $primInstBehavior=null,
                                    $primClassBehavior=null)
    {
        if (!$primClassBehavior)
            $primClassBehavior = "ClassOf$name";
        if (!class_exists($primClassBehavior))
            $this->defineInstanceBehaviorClass
                ($primClassBehavior, $superclass);

        if (!$primInstBehavior)
            $primInstnBehavior = $name;

        $this->_name = "$name class";
        $inst = new $primClassBehavior($this);
        $this->_thisClass = $inst->initWithName
            ($name, $superclass, $properties, $primInstBehavior);
        $this->_superclass = $superclass->klass();
        $superclass->addSubclass($this->_thisClass);
        return $this->_thisClass;
    }

    function defineInstanceBehaviorClass($name, $superclass)
    {
        $code = "class $name extends " .  get_class($superclass) . '{}';
        eval($code);
    }

    function isMetaclass()
    {
        return true;
    }

}

