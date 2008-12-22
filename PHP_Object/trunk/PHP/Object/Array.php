<?php
require_once 'PHP/Object.php';

class PHP_Object_Array extends PHP_Object implements Iterator, ArrayAccess
{
    protected $aliasMethods = array(
        'array_*',
    );

    public $argOffsets = array(
        'array_change_key_case' => 0,
        'array_chunk' => 0,
        'array_count_values' => 0,
        'array_diff_assoc' => 0,
        'array_diff_key' => 0,
        'array_diff_uassoc' => 0,
        'array_diff_ukey' => 0,
        'array_diff' => 0,
        'array_fill_keys' => 0,
        'array_filter' => 0,
        'array_flip' => 0,
        'array_intersect_assoc' => 0,
        'array_key_exists' => 1,
        'array_keys' => 0,
        'array_map' => 1,
        'array_merge_recursive' => 0,
        'array_pad' => 0, 
        'array_pop' => 0, 
        'array_push' => 0, 
        'array_rand' => 0, 
        'array_reduce' => 0, 
        'array_reverse' => 0, 
        'array_search' => 1,
        'array_shift' => 0,
        'array_slice' => 0,
        'array_splice' => 0,
        'array_sum' => 0,
        'array_udiff_assoc' => 0,
        'array_udiff_uassoc' => 0,
        'array_udiff' => 0,
        'array_uintersect_assoc' => 0,
        'array_uintersect_uassoc' => 0,
        'array_uintersect' => 0,
        'array_unique' => 0,
        'array_unshift' => 0,
        'array_values' => 0,
        'array_walk_recursive' => 0,
        'array_walk' => 0,
        'arsort' => 0,
        'asort' => 0,
        'call_user_func_array' => 1,
        'call_user_method_array' => 2,
        'count' => 0,
        'current' => 0,
        'each' => 0,
        'end' => 0,
        'extract' => 0,
        'getopt' => 1,
        'implode' => 1,
        'in_array' => 1,
        'join' => 1,
        'key' => 0,
        'krsort' => 0,
        'ksort' => 0,
        'mb_convert_variables' => 2,
        'mb_decode_numericentity' => 1,
        'mb_encode_numericentity' => 1,
        'mysql_fetch_object' => 2,
        'natcasesort' => 0,
        'natsort' => 0,
        'next' => 0,
        'parse_str' => 1,
        'pg_convert' => 2,
        'pg_copy_from' => 2,
        'pos' => 0,
        'prev' => 0,
        'preg_match' => 2,
        'preg_match_all' => 2,
        'reset' => 0,
        'rsort' => 0,
        'setlocale' => 1,
        'shuffle' => 0,
        'sizeof' => 0,
        'sort' => 0,
        'strtr' => 1,
        'uasort' => 0,
        'uksort' => 0,
        'usort' => 0,
        'uniqid' => 1,
        'vfprintf' => 2,
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
