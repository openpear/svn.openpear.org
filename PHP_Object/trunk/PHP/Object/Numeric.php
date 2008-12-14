<?php
require_once 'PHP/Object.php';

class PHP_Object_Numeric extends PHP_Object
{

    protected $aliasMethods = array(
        'money_format' => 1,
        'number_format' => 0,
        'similar_text' => 1,
    );

    public $argOffsets = array();

}
