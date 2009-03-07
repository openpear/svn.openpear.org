<?php

class PhobProperty extends PhobObject
{

    public static $defaultValue;

    protected $_name;
    protected $_owner;
    protected $_valueType;
    protected $_memberName;
    protected $_allowsNull = true;
    protected $_isOptional = true;
    protected $_isTransient = false;
    protected $_isReadOnly = false;
    protected $_defaultValue;
    protected $_defaultValueInitializer;
    protected $_defaultValueInitializerFunc;
    protected $_valueTransformerName;
    protected $_valueValidators = array();

    function initWithPropertyBuilder($b, $owner)
    {
        parent::init();
        $this->_name = $b->name();
        $this->_owner = $owner;
        $this->_valueType = $b->valueType();
        $this->_memberName = $b->memberName();
        $this->_allowsNull = $b->allowsNull();
        $this->_isOptional = $b->isOptional();
        //$this->_isTransient = $b->isTransient();
        $this->_isReadOnly = $b->isReadOnly();
        $this->_defaultValue = $b->defaultValue();
        $this->_defaultValueInitializer = $b->defaultValueInitializer();
        $this->_valueTransformerName = $b->valueTransformerName();
        $this->_valueValidators = $b->valueValidators();
        return $this;
    }

    function ownerClass()
    {
        return $this->_owner;
    }

    function name()
    {
        return $this->_name;
    }

    function keyPath()
    {
        return $this->_name;
    }

    function valueType()
    {
        return $this->_valueType;
    }

    function memberName()
    {
        return $this->_memberName;
    }

    function allowsNull()
    {
        return $this->_allowsNull;
    }

    function isOptional()
    {
        return $this->_isOptional;
    }

    function isTransient()
    {
        return $this->_isTransient;
    }

    function isReadOnly()
    {
        return $this->_readOnly;
    }

    function defaultValue()
    {
        return $this->_defaultValue;
    }

    function defaultValueInitializer()
    {
        return $this->_defaultValueInitializer;
    }

    function defaultValueWithObject($object)
    {
        return call_user_func($this->defaultValueInitializerFunction(),
                              $object);
    }

    function defaultValueInitializerFunction()
    {
        if ($this->_defaultValueInitializer) {
            if (!$this->_defaultValueInitializerFunc) {
                $this->_defaultValueInitializerFunc =
                    create_function('$this',
                                    $this->_defaultValueInitializer);
            }
            return $this->_defaultValueInitializerFunc;
        } else
            return null;
    }

    function valueTransformerName()
    {
        return $this->_valueTransformerName;
    }

    function valueValidators()
    {
        return $this->_valueValidators;
    }

    function validateValue($value)
    {
        if ($this->_valueType) {
            switch ($this->_valueType) {
                case 'string':
                    if (!is_string($value))
                        throw new PhobValidationException
                            ("value is not string for '$this->_name'");
                    break;
            }
        }
        foreach ($this->_valueValidators as $validator)
            $value = $validator->validateValue($value);
        return $value;
    }

}

Phoblinks()->defineClass('PhobProperty');

class PhobDefaultValue {}

PhobProperty::$defaultValue = new PhobDefaultValue();

