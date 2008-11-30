<?php
require_once "PHP/Object.php";

class PHP_Object_String extends PHP_Object
{
    protected $aliasMethods = array(
        'str_*',
        'str*',
    );

    public $argOffsets =  array(
        'array_reduce' => 1,
        'fprintf' => 1,
        'get_parent_class' => 0,
        'is_a' => 1,
        'is_subclass_of' => 1,
        'method_exists' => 1,
        'uasort' => 1, 
        'uksort' => 1, 
        'uniqid' => 0,
        'usort' => 1, 
    );

}
