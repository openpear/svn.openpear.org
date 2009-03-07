<?php

class ClassOfPhobValueTransformer extends ClassOfPhobObject
{

    private $_byName = array();

    function valueTransformerNames()
    {
        return array_keys($this->_byName);
    }

    function valueTransformerForName($name)
    {
        if (array_key_exists($name, $this->_byName))
            return $this->_byName[$name];
        else
            return null;
    }

    function setValueTransformerForName($transformer, $name)
    {
        $this->_byName[$name] = $transformer;
    }

    function allowsReverseTransformation()
    {
        return false;
    }

    function transfarmedValueClass()
    {
        throw new Exception('+transformedValueClass() must be implemented.');
    }

}


class PhobValueTransformer extends PhobObject
{

    const NegateBooleanTransformerName = 'NegateBoolean';
    const IsNullTransformerName = 'IsNull';
    const IsNotNullTransformerName = 'IsNotNull';

    function transformedValue($value)
    {
        return $value;
    }

    function reverseTransformedValue($value)
    {
        if ($this->klass()->allowsReverseTransformation())
            return $this->transformedValue($value);
        else
            throw new Exception('Cannot reverse transform the value.');
    }

}


class ClassOfPhobNegateBooleanValueTransformer extends ClassOfPhobValueTransformer
{

    function allowsReverseTransformation()
    {
        return true;
    }

}


class PhobNegateBooleanValueTransformer extends PhobValueTransformer
{

    function transformedValue($value)
    {
        return !(boolean)$value;
    }

    function reverseTransformedValue($value)
    {
        return !(boolean)$value;
    }

}


class PhobIsNullValueTransformer extends PhobValueTransformer
{

    function transformedValue($value)
    {
        return is_null($value);
    }

}


class PhobIsNotNullValueTransformer extends PhobValueTransformer
{

    function transformedValue($value)
    {
        return !is_null($value);
    }

}


Phoblinks()->defineClass('PhobValueTransformer');
Phoblinks()->defineClass('PhobNegateBooleanValueTransformer');
Phoblinks()->defineClass('PhobIsNullValueTransformer');
Phoblinks()->defineClass('PhobIsNotNullValueTransformer');

PhobValueTransformer()
    ->setValueTransformerForName(PhobNegateBooleanValueTransformer()->make(),
                                 PhobValueTransformer::
                                 NegateBooleanTransformerName);
PhobValueTransformer()
    ->setValueTransformerForName(PhobIsNullValueTransformer()->make(),
                                 PhobValueTransformer::IsNullTransformerName);
PhobValueTransformer()
    ->setValueTransformerForName(PhobIsNotNullValueTransformer()->make(),
                                 PhobValueTransformer::IsNotNullTransformerName);

