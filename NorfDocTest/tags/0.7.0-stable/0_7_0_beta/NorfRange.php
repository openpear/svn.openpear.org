<?php

class NorfRange implements IteratorAggregate
{

    static function zeroRange()
    {
        if (!$this->_zero)
            $this->_zero = new self(0, 0);
        return $this->_zero;
    }

    function __construct($lc, $len, $bound=true)
    {
        $this->_bound = $bound;
        $this->setBeginningLocation($lc);
        $this->setLength($len);
    }

    function beginningLocation()
    {
        return $this->_begin;
    }

    function setBeginningLocation($lc)
    {
        $this->_begin = $lc + !$this->_bound;
    }

    function endingLocation()
    {
        return $this->_end;
    }

    function setEndingLocation($lc)
    {
        $this->_end = $lc - !$this->_bound;
        $this->_len = $this->_end - $this->_begin;
    }

    function length()
    {
        return $this->_len;
    }

    function setLength($len)
    {
        $this->_len = $len;
        $this->_end = $this->_begin + $len - !$this->_bound;
    }

    function containsLocation($lc)
    {
        return $this->_begin <= $lc && $lc <= $this->_end;
    }

    function progressiveValues()
    {
        $values = new NorfArray();
        for ($i = $this->_begin; $i <= $this->_end; $i++)
            $values->addObject($i);
        return $values;
    }

    function containsRange($range)
    {
        return $this->_begin <= $range->beginningLocation() and
            $this->_end >= $range->endingLocation();
    }
 
    function isSubrangeOfRange($range)
    {
        return $this->_begin >= $range->beginningLocation() and
            $this->_end <= $range->endingLocation();
    }

    function intersectsRange($range)
    {
        if ($this->_begin >= $range->beginningLocation() and
            $this->_begin < $range->endingLocation())
            return true;
        elseif ($range->beginningLocation() >= $this->_begin and
                $range->beginningLocation() < $this->_end)
            return true;
        else
            return false;
    }

    function getIterator()
    {
        return $this->rangeEnumerator();
    }

    function rangeEnumerator()
    {
        return new NorfRangeEnumerator($this);
    }

    function description()
    {
        $str = $this->beginningLocation() . '..' . $this->endingLocation();
        return $this->descriptionWithContentString($str);
    }

}


class NorfRangeEnumerator implements Iterator
{

    function __construct($range)
    {
        $this->_range = $range;
    }

    function rewind()
    {
        $this->_i = $this->_range->beginningLocation();
    }

    function current()
    {
        return $this->_i;
    }

    function key()
    {
        return $this->_i;
    }

    function next()
    {
        $this->_i++;
    }

    function valid()
    {
        return $this->_range->endingLocation() > $this->_i;
    }

}


class NorfOutOfRangeException extends Exception
{

    function __construct($availableRange, $msg)
    {
        parent::__construct($msg);
        $this->_availableRange = $availableRange;
    }

    function availableRange()
    {
        return $this->_availableRange();
    }

}


class NorfRangeOutOfRangeException extends NorfOutOfRangeException
{

    function __construct($range, $availableRange, $msg)
    {
        parent::__construct($availableRange, $msg);
        $this->_range = $range;
    }

    function range()
    {
        return $this->_range;
    }

}


class NorfIndexOutOfRangeException extends NorfOutOfRangeException
{

    function __construct($i, $availableRange, $msg)
    {
        parent::__construct($availableRange, $msg);
        $this->_i = $i;
    }

    function index()
    {
        return $this->_i;
    }

}

