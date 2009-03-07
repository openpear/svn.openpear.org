<?php

abstract class PhobValueValidator extends PhobObject
{

    abstract function validateValue($value);

}


class PhobStringValueValidator extends PhobValueValidator
{

    protected $_allowsEmpty = true;
    protected $_maxLen = -1;
    protected $_minLen = -1;

    function allowsEmpty()
    {
        return $this->_allowsEmpty;
    }

    function setAllowsEmpty($flag)
    {
        $this->_allowsEmpty = $flag;
    }

    function maximumLength()
    {
        return $this->_maxLen;
    }

    function setMaximumLength($value)
    {
        $this->_maxLen = $value;
    }

    function minimumLength()
    {
        return $this->_minLen;
    }

    function setMinimumLength($value)
    {
        $this->_minLen = $value;
    }

    function validateValue($value)
    {
        $len = strlen($value);
        if ($len === 0 && !$this->_allowsEmpty)
            throw new PhobValidationException('The Value must not be empty.');
        else if ($len < $this->_minLen)
            throw new PhobValidationException
                ('Length of the value must be more than or equal to ' .
                 "$this->_minLen. ($len)");
        else if ($this->_maxLen >= 0 && $len > $this->_maxLen)
            throw new PhobValidationException
                ('Length of the value must be less than or equal to ' .
                 "$this->_maxLen. ($len)");
        else
            return $value;
    }

}

Phoblinks()->defineClass('PhobValueValidator');
Phoblinks()->defineClass('PhobStringValueValidator');

