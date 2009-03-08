<?php

class PhobTrait extends PhobObject
{

    protected $_isFixed = false;
    protected $_name;
    protected $_meths;

    function init()
    {
        parent::init();
        $this->_meths = array();
        return $this;
    }

    function initWithName($name, $meths)
    {
        parent::init();
        $this->_name = $name;
        $this->_meths = $meths;
        $this->_isFixed = false;
        return $this;
    }

    function initWithContentsOfPHPClass($PHPClass, $name=null)
    {
        parent::init();
        if (!$name)
            $name = $PHPClass;
        $meths = array();
        $refClass = new ReflectionClass($PHPClass);
        foreach ($refClass->getMethods() as $refMeth) {
            if ($refMeth->isStatic())
                $meths[$refMeth->getName()] =
                    array($PHPClass, $refMeth->getName());
        }
        return $this->initWithName($name, $meths);
    }

    function initWithTrait($trait)
    {
        return $this->initWithName(null, $trait->allMethods());
    }

    function copy()
    {
        return $this->_class->alloc()->initWithTrait($this);
    }

    function isFixed()
    {
        return $this->_isFixed;
    }

    function beFixed()
    {
        $this->_isFixed = true;
    }

    function name()
    {
        return $this->_name;
    }

    protected function validateIsFixed()
    {
        if ($this->_isFixed)
            throw new Exception('This trait is already fixed.');
    }

    function allMethods()
    {
        return $this->_meths;
    }

    function addMethod($className, $methName, $rename=null)
    {
        $this->validateIsFixed();
        if (!$rename)
            $rename = $methName;
        $this->_meths[$rename] = array($className, $methName);
    }

    function removeMethodForName($name)
    {
        $this->validateIsFixed();
        unset($this->_meths[$name]);
    }

    function renameMethod($name, $replace)
    {
        $this->validateIsFixed();
        $assoc = $this->_meths[$name];
        $this->removeMethodForName($name);
        $this->addMethod($assoc[0], $assoc[1], $replace);
    }

    function unionTrait($trait)
    {
        $this->validateIsFixed();
        $newTrait = $this->traitByUnioningTrait($trait);
        $this->_meths = $newTrait->_meths;
    }

    function traitByUnioningTrait($trait)
    {
        $meths = $this->_meths;
        foreach ($trait->allMethods() as $methName => $assoc) {
            if (array_key_exists($methName, $this->_meths))
                $this->traitConflict($methName, $trait);
            else
                $meths[$methName] = $assoc;
        }
        return $this->klass()->alloc()->initWithName(null, $meths);
    }

    function traitConflict($methName, $trait)
    {
        throw new PhobTraitConflictException
            (sprintf('%s(): a conflict between %s and %s',
                     $methName, $this->_name, $trait->name()));
    }

    function minusTrait($trait)
    {
        $this->validateIsFixed();
        $newTrait = $this->traitByMinusingTrait($trait);
        $this->_meths = $newTrait->_meths;
    }

    function traitByMinusingTrait($trait)
    {
        $minusMeths = $trait->allMethods();
        $meths = array();
        foreach ($this->_meths as $methName => $assoc) {
            if (!array_key_exists($methName, $minusMeths))
                $meths[$methName] = $assoc;
        }

        return $this->klass()->alloc()->initWithName(null, $meths);
    }

    static function argumentsRepresentations($className, $methName)
    {
        $refClass = new ReflectionClass($className);
        $refMeth = $refClass->getMethod($methName);

        $args = '(';
        $decl = '(';
        $arith = $refMeth->getNumberOfParameters();
        if ($arith > 1)
            $args .= '$this,';
        else
            $args .= '$this';
        $i = 1;
        foreach ($refMeth->getParameters() as $refParam) {
            // ignore first argument (receiver)
            if ($i == 1)
                continue;

            if ($refParamClass = $refParam->getClass())
                $decl .= $refParamClass->getName() . ' ';
            if ($refParam->isPassedByReference())
                $mehtCode .= '&';

            $arg = '$' . $refParam->getName();
            $decl .= $arg;
            $args .= $arg;
            if ($i < $arith)
                $args .= ',';

            if ($refParam->isOptional())
                $decl .= '=' .
                    var_export($refParam->getDefaultValue(), 1);
            if ($i < $arith)
                $decl .= ',';

            $i++;
        }
        $args .= ')';
        $decl .= ')';
        return array($decl, $args);
    }

}

Phoblinks()->defineClass('PhobTrait');


class PhobTraitConflictException extends Exception {}

