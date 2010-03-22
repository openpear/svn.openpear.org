<?php
namespace openpear\services\calil;

abstract class Query
{
    const KEY_LIBRARY = 'library';
    const KEY_CHECK = 'check';

    /**
     *
     */
    public static function factory($path, array $q)
    {
        if (strtolower($path) == self::KEY_LIBRARY) {
            return new openpear\services\calil\query\Library($q);
        } else if (strtolower($path) == self::KEY_CHECK) {
            return new openpear\services\calil\query\Check($q);
        }
        
        throw new openpear\services\calil\InvalidArgumentException();
    }

    /**
     * No validate factory
     *
     */
    public static function library(array $q)
    {

    }

    /**
     * No validate factory
     */
    public static function check(array $q)
    {
    }

}

