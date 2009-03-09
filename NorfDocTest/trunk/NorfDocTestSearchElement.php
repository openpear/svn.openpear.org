<?php

abstract class NorfDocTestSearchElement
{
    
    abstract function matchesObject($obj);

}


class NorfDocTestCompoundSearchElement extends NorfDocTestSearchElement
{

    const AND_TYPE = 0;
    const OR_TYPE = 1;
    const NOT_TYPE = 2;

    private $_type;
    private $_subels;

    static function andSearchElementWithSubsearchElements($els)
    {
        return new self(self::AND_TYPE, $els);
    }

    static function orSearchElementWithSubsearchElements($els)
    {
        return new self(self::OR_TYPE, $els);
    }

    static function notSearchElementWithSubsearchElement($el)
    {
        return new self(self::NOT_TYPE, new NorfArray($el));
    }

    function __construct($type, $subels)
    {
        $this->_type = $type;
        $this->_subels = NorfArray::arrayWithArray($subels);
    }

    function subsearchElements()
    {
        return $this->_subels;
    }

    function matchesObject($obj)
    {
        switch ($this->_type) {
        case self::AND_TYPE:
            foreach ($this->_subels as $el)
                if (!$el->matchesObject($obj))
                    return false;
            return true;
        case self::OR_TYPE:
            foreach ($this->_subels as $el)
                if ($el->matchesObject($obj))
                    return true;
            return false;
        case self::NOT_TYPE:
            return !$this->_subels->objectAtIndex(0)->matchesObject($obj);
        }
    }

}


class NorfDocTestBooleanSearchElement extends NorfDocTestSearchElement
{

    private static $_true;
    private static $_false;
    private $_value;

    static function trueSearchElement()
    {
        if (!self::$_true)
            self::$_true = new self(true);
        return self::$_true;
    }

    static function falseSearchElement()
    {
        if (!self::$_false)
            self::$_false = new self(false);
        return self::$_false;
    }

    function __construct($value)
    {
        $this->_value = $value;
    }

    function matchesObject($obj)
    {
        return $this->_value;
    }

}


class NorfDocTestNameSearchElement extends NorfDocTestSearchElement
{

    private $_name;

    function __construct($name)
    {
        $this->_name = $name;
    }

    function name()
    {
        return $this->_name;
    }

    function matchesObject($name)
    {
        return $this->_name == $name;
    }

}


class NorfDocTestNamePatternSearchElement extends NorfDocTestSearchElement
{

    private $_namePattern;

    function __construct($namePattern)
    {
        $this->_namePattern = $namePattern;
    }

    function namePattern()
    {
        return $this->_namePattern;
    }

    function matchesObject($name)
    {
        return preg_match($this->_namePattern, $name);
    }

}

