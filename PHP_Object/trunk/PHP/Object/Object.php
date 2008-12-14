<?php
require_once "PHP/Object.php";

class PHP_Object_Object extends PHP_Object
{
    public $argOffsets =  array(
        'call_user_method_array' => 1,        
        'call_user_method' => 1,
        'count' => 0,
        'date_add' => 0,
        'date_create' => 1,
        'date_date_set' => 0,
        'date_format' => 0,
        'date_isodate_set' => 0,
        'date_modify' => 0,
        'date_offset_get' => 0,
        'date_sub' => 0,
        'get_class' => 0,
        'get_parent_class' => 0,
        'timezone_transitions_get' => 0,
    );

    public function __clone()
    {
        $this->data = clone $this->data;
    } 
}
