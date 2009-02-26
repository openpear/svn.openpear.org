<?php

class NorfDocTestMethodDescription extends NorfDocTestBlockStore
{

    private $_name;
    private $_classDesc;

    function __construct($name)
    {
        parent::__construct();
        $this->_name = $name;
    }

    function module()
    {
        return $this->_classDesc->module();
    }

    function name()
    {
        return $this->_name;
    }

    function signature()
    {
        return $this->_classDesc->name() . "::$this->_name()";
    }

    function classDescription()
    {
        return $this->_classDesc;
    }

    function setClassDescription($desc)
    {
        $this->_classDesc = $desc;
    }

    function isAbstract()
    {
        return $this->reflector()->isAbstract();
    }

    function reflector()
    {
        return $this->_classDesc->reflector()->getMethod($this->_name);
    }

    function testedMethodOfSuperclass()
    {
        $class = $this->_parent->testedClassOfSuperclass();
        if ($class)
            return $class->submoduleNamed($this->_name);
        else
            return null;
    }

    function hasTestsWithClass($class)
    {
        foreach ($this->_tests as $test)
            if ($test->canTestWithClass($class))
                return true;

        if ($this->isOverridden()) {
            $submeth = $this;
            $meth = $this->testedMethodOfSuperclass();
            while ($meth && $submeth->executesSupertests()) {
                foreach ($meth->tests() as $test)
                    if (!$test->isToDo())
                        return true;
                $submeth = $meth;
                $meth = $meth->testedMethodOfSuperclass();
            }
            return false;
        } else
            return false;
    }

    function isOverridden()
    {
        $methRef = $this->reflector();
        $isConst = $methRef->isConstructor();
        $classRef= $methRef->getDeclaringClass();
        if ($classRef->getName() == $this->_parent->name()) {
            try {
                while ($classRef = $classRef->getParentClass()) {
                    if ($isConst) {
                        if ($classRef->getConstructor())
                            return true;
                    } else if ($classRef->getMethod($this->_name))
                        return true;
                }
                return false;
            } catch (ReflectionException $e) {
                return false;
            }
        } else
            return false;
    }

    function allTests()
    {
        if (!$this->isOverridden())
            return $this->tests();
        else {
            $tests = new NorfArray();
            $tests->addObjectsFromArray($this->allSupertests());
            $tests->addObjectsFromArray($this->tests());
            return $tests;
        }
    }

    function allSupertests()
    {
        $tests = new NorfArray();
        $submeth = $this;
        $meth = $this->testedMethodOfSuperclass();
        while ($meth && $submeth->executesSupertests()) {
            foreach ($meth->tests() as $test)
                if (!$test->isToDo())
                    $tests->addObject($test);
            $submeth = $meth;
            $meth = $meth->testedMethodOfSuperclass();
        }
        return $tests;
    }

}

