<?php
require_once 'PHP/Object.php';

class PHP_Object_Array extends PHP_Object implements Iterator, ArrayAccess
{
    protected $aliasMethods = array(
        'array_*',
        '*_array',
    );

    public $argOffsets = array(
        'array_key_exists' => 1,
        'array_keys' => 0,
        'array_map' => 1,
        'array_pad' => 0, 
        'array_push' => 0, 
        'array_search' => 1,
        'array_unshift' => 0,
        'call_user_func_array' => 1,
        'call_user_method_array' => 2,
        'implode' => 1,
        'in_array' => 1,
        'join' => 1,
        'mb_convert_variables' => 2,
        'mb_decode_numericentity' => 1,
        'mb_encode_numericentity' => 1,
        'parse_str' => 1,
        'preg_match_all' => 2,
        'uniqid' => 1,
        'vprintf' => 1,
        'vsprintf' => 1,
    );

    /**
     * implements ArrayAccess
     **/
    public function offsetExists($offset)
    {
        return isset($this->data[$this->revert($offset)]);
    }

    public function offsetGet($offset)
    {
        return self::factory($this->data[$this->revert($offset)]);        
    }

    public function offsetSet($offset, $value)
    {
        $offset = $this->revert($offset);
        if (is_null($offset)) {
            $this->data[] = $value;
        }
        $this->data[$offset] = $value;
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
