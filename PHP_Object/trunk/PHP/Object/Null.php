<?php
require_once "PHP/Object.php";

class PHP_Object_Null extends PHP_Object
{
    protected $data = null;
    
    protected $argOffsets = array(
        'array_fill' => 2,
        'array_fill_keys' => 1,
        'array_keys' => 1,
        'array_pad' => 2,
        'array_push' => 1,
        'array_search' => 0,
        'array_unshift' => 1,
        'assert' => 0,
        'call_user_func' => 1,
        'call_user_method' => 2,
        'debug_zval_dump' => 0,
        'define' => 1,
        'filter_var' => 0,
        'floatval' => 0,
        'gettype' => 0,
        'in_array' => 0,
        'intval' => 0,
        'is_array' => 0,
        'is_bool' => 0,
        'is_double' => 0,
        'is_float' => 0,
        'is_int' => 0,
        'is_integer' => 0,
        'is_long' => 0,
        'is_null' => 0,
        'is_numeric' => 0,
        'is_object' => 0,
        'is_real' => 0,
        'is_resource' => 0,
        'is_scalar' => 0,
        'is_string' => 0,
        'json_encode' => 0,
        'pack' => 1,
        'print_r' => 0,
        'serialize' => 0,
        'settype' => 0,
        'shm_put_var' => 2,
        'strval' => 0,
        'var_dump' => 0,
        'var_export' => 0,
        'wddx_serialize_value' => 0,
    );

    protected function __construct() {}

    protected static function getInstance()
    {
        static $instance = null;
        if (is_null($instance)) {
            $instance = new self;
        } 
        return $instance;
    }
}
