<?php
require_once 'PHP/Object/Numeric.php';

class PHP_Object_Numeric_Integer extends PHP_Object_Numeric
{

    protected $aliasMethods = array();

    public $argOffsets =  array(
        'array_chunk' => 1,
        'array_rand' => 1,
        'array_reduce' => 2,
        'array_slice' => 1,
        'array_splice' => 1,
        'arsort' => 1,
        'asort' => 1,
        'count' => 1,
        'krsort' => 1,
        'ksort' => 1,
        'rsort' => 1,
        'sort' => 1,
    );

}
