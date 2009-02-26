<?php

class NorfDocTestClassDescription extends NorfDocTestBlockStore
{

    private $_name;
    private $_module;
    private $_methDescs;

    function __construct($name)
    {
        parent::__construct();
        $this->_name = $name;
        $this->_methDescs = new NorfArray();
    }

    function signature()
    {
        return $this->_name;
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

    function methodDescriptions()
    {
        return $this->_methDescs;
    }

    function addMethodDescription($desc)
    {
        $this->_methDescs->addObject($desc);
        $desc->setClassDescription($this);
    }

    function removeMethodDescription($desc)
    {
        $this->_methDescs->removeObject($desc);
        $desc->setClassDescription($null);
    }

    function isAbstract()
    {
        return $this->reflector()->isAbstract();
    }

    function reflector()
    {
        return new ReflectionClass($this->_name);
    }

    function testedClassOfSuperclass()
    {
        $ref = $this->reflector();
        $store = $this->testStore();
        while ($ref = $ref->getParentClass())
            if ($class = $store->testedClassNamed($ref->getName()))
                return $class;
    }

    function isSubtestedClassOfTestedClass($class)
    {
        $temp = $this;
        while ($temp) {
            if ($temp == $class)
                return true;
            else
                $temp = $temp->testedClassOfSuperclass();
        }
        return false;
    }

}

