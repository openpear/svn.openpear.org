<?php
require_once "PHP/Object.php";

class PHP_Object_Boolean extends PHP_Object
{
    public $argOffsets =  array(
        'array_chunk' => 2,
        'array_keys' => 2,
        'array_reverse' => 1,
        'array_search' => 0,
        'array_slice' => 3,
        'class_exists' => 1,
        'debug_backtrace' => 0,
        'get_defined_constants' => 0,
        'get_loaded_extensions' => 0,
        'gettimeofday' => 0,
        'htmlentities' => 3,
        'htmlspecialchars' => 3,
        'in_array' => 2,
        'ini_get_all' => 1,
        'interface_exists' => 1,
        'localtime' => 1,
        'md5_file' => 1,
        'md5' => 1,
        'microtime' => 0,
        'memory_get_peak_usage' => 0,
        'memory_get_usage' => 0,
        'mysql_connect' => 3,
        'nl2br' => 1,
        'ob_get_status' => 0,
        'ob_implicit_flush' => 0,
        'ob_start' => 2,
        'sha1_file' => 1,
        'sha1' => 1,
        'strchr' => 2,
        'stristr' => 2,
        'strstr' => 2,
        'substr_compare' => 4,
        'wordwrap' => 4,
    );

}
