<?php
require_once "PHP/Object.php";

class PHP_Object_Object extends PHP_Object
{
    public function __clone()
    {
        $this->data = clone $this->data;
    } 
}
