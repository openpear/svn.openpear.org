<?php

class PhobObject
{

    protected $_class;
    protected $_hash;

    function __construct($class=null)
    {
        $this->_class = $class;
    }

    function init()
    {
        return $this;
    }

    function __toString()
    {
        return '<' . get_class($this) . '>';
    }

    function klass()
    {
        return $this->_class;
    }

    function isMemberOfClass($class)
    {
        return $this->_class === $class;
    }

    function isKindOfClass($class)
    {
        return $this->_class->isSubclassOfClass($class);
    }

    function respondsToSelector($sel)
    {
        return method_exists($this, $sel);
    }

    function hash()
    {
        if ($this->_hash !== null)
            return $this->_hash;
        else
            return $this->_hash = spl_object_hash($this);
    }

    function isEqual($object)
    {
        return $this === $object ||
            (is_subclass_of($object, 'PhobObject') &&
             $this->hash() === $object->hash());
    }

    function isClass()
    {
        return false;
    }

    function primitiveValueForKey($key)
    {
        if (!array_key_exists($key, $this)) {
            if (array_key_exists("_$key", $this))
                $key = "_$key";
            else
                throw new PhobUnknownKeyException($key);
        }
        return $this->$key;
    }

    function setPrimitiveValueForKey($value, $key)
    {
        if (!array_key_exists($key, $this)) {
            if (array_key_exists("_$key", $this))
                $key = "_$key";
            else
                throw new PhobUnknownKeyException($key);
        }
        $this->$key = $value;
    }

    function doesNotRecognizeSelector($sel)
    {
        if ($this->isClass()) {
            $type = '+';
            $name = $this->_name;
        } else {
            $type = '-';
            $name = $this->_class->name();
        }
        throw new PhobUnknownMessageException
            ("${type}[$name $sel]: selector not recognized");
    }

    function __call($name, $args)
    {
        return $this->doesNotRecognizeSelector($name);
    }

}


class PhobUnknownMessageException extends Exception
{
}

