<?php

class ClassOfPhoblinks extends ClassOfPhobObject
{

    public $_classByName = array();
    public $_traitByName = array();

    function defineClass($className, $superclass=null, $propBuilders=null,
                         $primInstBehavior=null, $primClassBehavior=null)
    {
        if (!class_exists($className))
            throw new InvalidArgumentException
                ("class $className is not defined.");

        if (!$superclass) {
            $superName = get_parent_class($className);
            if ($superName != 'PhobObject' &&
                !is_subclass_of($superName, 'PhobObject'))
                throw new InvalidArgumentException
                    ("class $superName is not a subclass of PhobObject");

            $superclass = $this->classNamed($superName);
        }

        $metaclass = PhobMetaclass()->alloc()
            ->initWithInstanceBehaviorName($className, $superclass,
                                           $propBuilders,
                                           $primInstBehavior,
                                           $primClassBehavior);
        $this->addClass($metaclass->instanceBehavior());
    }

    function addClass($class)
    {
        $name = $class->name();
        $this->_classByName[$name] = $class;
        eval("function $name() { return " .
             "Phoblinks::\$sharedInstance->_classByName['$name']; }");
    }

    function classNamed($name)
    {
        if (array_key_exists($name, $this->_classByName))
            return $this->_classByName[$name];
        else
            return null;
    }

    function toDefineClass($className, $superclass=null, $builderClass=null)
    {
        if (array_key_exists($className, $this->_classByName))
            throw new InvalidArgumentException
                ("class $className is already defined.");

        return PhobClassBuilderCascade()->alloc()
            ->initWithClassName($className, $superclass, $builderClass);
    }

    function defineTrait($traitName, $PHPClass=null)
    {
        if ($this->traitNamed($traitName) !== null)
            throw new InvalidArgumentException
                ("Trait $traitName is already defined.");

        if (!$PHPClass)
            $PHPClass = $traitName;
        $trait = PhobTrait()->alloc()
            ->initWithContentsOfPHPClass($PHPClass, $traitName);
        $this->addTrait($trait);
        eval("function $traitName() { return Phoblinks::\$sharedInstance->_traitByName['$traitName']; }");
        return $trait;
    }

    function traitNamed($name)
    {
        if (array_key_exists($name, $this->_traitByName))
            return $this->_traitByName[$name];
        else
            return null;
    }

    function addTrait($trait)
    {
        $trait->beFixed();
        $name = $trait->name();
        $this->_traitByName[$name] = $trait;
    }

    function traitsUsedByPHPClass($name)
    {
        $traits = array();
        $refClass = new ReflectionClass($name);
        foreach ($refClass->getInterfaces() as $refIntf) {
            if (preg_match('/\GT(.+)/', $refIntf->getName(), $matches)) {
                $traitName = $matches[1];
                $trait = $this->traitNamed($traitName);
                if ($trait)
                    $traits[] = $trait;
            }
        }
        return $traits;
    }

    function error($msg)
    {
        die("Phoblinks: error: $msg\n");
    }

}


class Phoblinks extends PhobObject
{

    public static $sharedInstance;

}

