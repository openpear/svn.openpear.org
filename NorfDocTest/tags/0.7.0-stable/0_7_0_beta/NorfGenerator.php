<?php

interface NorfGenerator
{

    function hasNextObject();
    function nextObject();

}


abstract class NorfGeneratorIterator implements NorfGenerator, Iterator
{

    private $_i;

    function __construct()
    {
        $this->_i = 0;
    }

    function index()
    {
        return $this->_i;
    }

    function setIndex($i)
    {
        $this->_i = $i;
    }

    function incrementIndex()
    {
        $this->_i++;
    }

    function decrementIndex()
    {
        $this->_i--;
    }

    // do nothing
    function rewind() {}
    function next() {}

    function current()
    {
        return $this->nextObject();
    }

    function key()
    {
        return $this->_i;
    }

    function valid()
    {
        return $this->hasNextObject();
    }

}


abstract class NorfAssociationGeneratorIterator extends NorfGeneratorIterator
{

    private $_nextKey;
    private $_nextObj;

    abstract function nextKey();
    abstract function nextObjectForKey($key);

    function __construct()
    {
        parent::__construct();
    }

    function current()
    {
        $this->_nextKey = $this->nextKey();
        $this->_nextObj = $this->nextObjectForKey($this->_nextKey);
        return $this->_nextObj;
    }

    function key()
    {
        return $this->_nextKey;
    }

    function valid()
    {
        return $this->hasNextObject();
    }

    function nextObject() {}

}

