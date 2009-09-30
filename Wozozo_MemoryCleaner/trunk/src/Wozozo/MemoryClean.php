<?php
/**
 * 
 *
 */

/**
 * Wozozo_MemoryCleaner
 *
 */
class Wozozo_MemoryCleaner
{
    public function __construct()
    {
        foreach ($GLOBALS as $key => $value) {
            global $$key;
            $$key = NULL;
        }
    }
}

/*

// example
$data = array();
foreach (range(0, strlen(__FILE__)) as $no_use) {
    $data[] = file_get_contents(__FILE__);
}

echo 'before memory usage:' . memory_get_usage() . 'byte' . PHP_EOL;

$wozozo = new Wozozo_MemoryCleaner();

echo 'after memory usage:' . memory_get_usage() . 'byte' . PHP_EOL;
 */

