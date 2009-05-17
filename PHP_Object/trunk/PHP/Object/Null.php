<?php
require_once "PHP/Object.php";

class PHP_Object_Null extends PHP_Object
{
    protected $data;
    
    protected function __construct() {
        $this->configure = PHP_Object_Configure::getInstance(__CLASS__);
    }

    protected static function getInstance()
    {
        static $instance = null;
        if (is_null($instance)) {
            $instance = new self;
        } 
        return $instance;
    }
}
