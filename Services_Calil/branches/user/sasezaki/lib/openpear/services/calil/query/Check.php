<?php
namespace openpear\services\calil\query;
use openpear\services\calil\Query;

class Check extends Query implements \Iterator
{
    // boolean
    private $_polling = false;

    private $_query = array();
    private $_currentQuery = array();

    // @var string
    private $_session;

    public function __construct(array $query)
    {
        // validate();

        $this->_query = $query;
        $this->_currentQuery = $query;
    }

    
    public function setSession($session)
    {
        $this->_session = $session;
    }

    public function setPolling($flag)
    {
        $this->_polling = (boolean) $flag;
    }

    public function current()
    {
        return $this->_currentQuery;
    }

    public function next()
    {
       $query = $this->_query['appkey'];
       $query = $this->_query['format'];
       $query = $this->_session;

       $this->_currentQuery = $query;
    }

    public function rewind()
    {
        $this->_currentQuery = $this->_query;
    }

    public function valid()
    {
        return $this->_polling;
    }

    public function __set($key , $value)
    {
        //static::validate
        
        $this->_query[strotolower($key)] = $value;
    }

    public function validate($key, $value)
    {
    }

    public function toArray()
    {
        return $this->_currentQuery;
    }

    //not implemented
    public function __toString()
    {
    }

}

