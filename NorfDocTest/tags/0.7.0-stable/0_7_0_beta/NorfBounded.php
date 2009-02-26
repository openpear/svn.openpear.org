<?php

abstract class NorfBounded
{

    abstract function length();

    function beginningLocation()
    {
        return 0;
    }

    function endingLocation()
    {
        return $this->length();
    }

    function availableRange()
    {
        return new NorfRange($this->beginningLocation(),
                             $this->length());
    }

    function availableRangeFromLocation($lc, $len=null)
    {
        throw new NorfNotImplementedError();
    }

    function availableRangeInRange($range)
    {
        throw new NorfNotImplementedError();
    }

    function containsRange($range)
    {
        return $this->availableRange()->containsRange($range);
    }

    function containsLocation($lc)
    {
        return $this->availableRange()->containsLocation($lc);
    }

    function validateRange($range)
    {
        if (!$this->containsRange($range))
            throw $this->rangeOutOfRangeException($range);
    }

    function validateIndex($i)
    {
        if (!$this->containsLocation($i))
            throw $this->indexOutOfRangeException($i);
    }

    function rangeOutOfRangeException($range, $msg=null)
    {
        if (!$msg)
            $msg = "range " . $range->beginningLocation() . ".." .
               $range->endingLocation() . " out of range";
        return new NorfRangeOutOfRangeException($range,
                                                $this->availableRange(),
                                                $msg);
    }

    function indexOutOfRangeException($i, $msg=null)
    {
        if (!$msg)
            $msg = "index $i out of range";
        return new NorfIndexOutOfRangeException($i,
                                                $this->availableRange(),
                                                $msg);
    }

}

