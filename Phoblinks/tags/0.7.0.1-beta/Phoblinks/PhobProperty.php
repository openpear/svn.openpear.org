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
        if (!$this->_allowsNull && $value === null)
            throw new PhobValidationException
                ("$this->_name: the value must not be null.");

        if ($this->_valueType) {
            $msg = null;
            if ($this->_valueType === 'string' && !is_string($value))
                $msg = "$this->_name: the value must be a string.";
            else if ($this->_valueType === 'boolean' && !is_bool($value))
                $msg = "$this->_name: the value must be a boolean value.";
            else if ($this->_valueType === 'array' && !is_array($value))
                $msg = "$this->_name: the value must be an array.";
            else if ($this->_valueType === 'int' && !is_int($value))
                $msg = "$this->_name: the value must be an integer value.";
            else if ($this->_valueType === 'float' && !is_float($value))
                $msg = "$this->_name: the value must be a floating-point number.";
            else if ($this->_valueType === 'numeric' && !is_numeric($value))
                $msg = "$this->_name: the value must be a numeric value.";
            else if (class_exists($this->_valueType) &&
                     !is_a($value, $this->_valueType))
                $msg = "$this->_name: the value must be an " .
                    "instance of $this->_valueType.";
            else if (is_a($this->_valueType, 'PhobBehavior') &&
                     (!is_a($value, 'PhobObject') ||
                      !$value->isKindOfClass($this->_valueType)))
                $msg = "$this->_name: the value must be an instance of " .
                    $this->_valueType->name() . '.';

            if ($msg)
                throw new PhobValidationException($msg);
        }
        foreach ($this->_valueValidators as $validator)
            $value = $validator->validateValue($value);
        return $value;
    }

}

Phoblinks()->defineClass('PhobProperty');

class PhobDefaultValue {}

PhobProperty::$defaultValue = new PhobDefaultValue();

