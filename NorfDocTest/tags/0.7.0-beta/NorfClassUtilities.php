<?php

class NorfClassUtilities
{

    static function isMemberOfClass($obj, $class)
    {
        return get_class($obj) == $class;
    }

    static function isKindOfClass($obj, $class)
    {
        return self::isSubclassOfClass(get_class($obj), $class);
    }

    static function isSubclassOfClass($target, $compare)
    {
        return $target == $compare || is_subclass_of($target, $compare);
    }

    static function respondsToSelector($obj, $name)
    {
        $ref = new ReflectionClass(get_class($obj));
        return $ref->hasMethod($name);
    }

    static function instancesRespondToSelector($class, $name)
    {
        $ref = new ReflectionClass($class);
        return $ref->hasMethod($name);
    }

}

