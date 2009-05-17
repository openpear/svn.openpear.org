<?php
require_once "PHP/Object.php";

class PHP_Object_Boolean extends PHP_Object
{
    protected static function getInstance($data)
    {
        static $instances = array();
        $key = (integer)!!$data;
        if (!array_key_exists($key, $instances)) {
            $instances[$key] = new self($data);
        }
        return $instances[$key];
    }
}
