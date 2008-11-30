<?php
require_once "PHP/Object.php";

class PHP_Object_Object extends PHP_Object
{
    public $argOffsets =  array(
        'call_user_method_array' => 1,        
        'call_user_method' => 1,
        'get_class' => 0,
        'get_parent_class' => 0,
    );

    public function __clone()
    {
        $this->data = clone $this->data;
    } 
}
