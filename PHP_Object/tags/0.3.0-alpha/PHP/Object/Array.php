<?php
require_once 'PHP/Object.php';

class PHP_Object_Array extends PHP_Object implements Iterator, ArrayAccess
{
    /**
     * implements ArrayAccess
     **/
    public function offsetExists($offset)
    {
        return isset($this->data[$this->revert($offset)]);
    }

    public function offsetGet($offset)
    {
        return self::factory(&$this->data[$this->revert($offset)]);        
    }

    public function offsetSet($offset, $value)
    {
        $offset = $this->revert($offset);
        $value  = $this->revert($value);
        if (is_null($offset)) {
            $this->data[] = $value;
        } else {
            $this->data[$offset] = $value;
        }
    }

    public function offsetUnset($offset)
    {
        unset($this->data[$this->revert($offset)]);  
    }

    /**
     * implements Iterator
     **/
    public function rewind()
    {
        $this->__call('reset');
    }

    public function current()
    {
        return $this->__call('current');
    }

    public function key()
    {
        //return $this->__call('key');
        return key($this->data);
    }

    public function next()
    {
        return $this->__call('next');
    }

    public function valid()
    {
        // return $this->__call(current($this->data) !== false);
        return current($this->data) !== false;
    }

}
