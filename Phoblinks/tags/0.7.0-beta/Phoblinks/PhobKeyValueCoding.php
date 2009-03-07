<?php

interface PhobKeyValueCoding
{
    function valueForKey($key);
    function setValueForKey($value, $key);
}


interface PhobKeyValueCodingAdditions extends PhobKeyValueCoding
{

    function setValueForKeyPath($value, $keyPath);
    function valueForKeyPath($keyPath); 
}


interface PhobKeyValueCodingErrorHandling
{
    function handleQueryWithUnboundKey($key);
    function handlesetValueForUnboundKey($key);
}


class PhobKeyValueCodingKeyNotFoundException extends Exception
{

    function __construct($msg, $obj, $key)
    {
        parent::__construct($msg);
        $this->_obj = $obj;
        $this->_key = $key;
    }

    function key()
    {
        return $this->_key;
    }

    function object()
    {
        return $this->_obj;
    }

}


class PhobKeyValueCodingNotSupportedException extends Exception
{

    function __construct($obj, $msg)
    {
        parent::__construct($msg);
        $this->_obj = $obj;
    }

}


class PhobKeyValueCodingDefaultImplementation
{

    const PUBLIC_POLITE_GETTER_METHOD_FLAG  = 32;
    const PUBLIC_GETTER_METHOD_FLAG         = 16;
    const PUBLIC_TESTING_METHOD_FLAG        = 8;
    const PRIVATE_POLITE_GETTER_METHOD_FLAG = 4;
    const PRIVATE_METHOD_FLAG               = 2;
    const PRIVATE_TESTING_METHOD_FLAG       = 1;

    const PRIVATE_IVAR_FLAG         = 8;
    const PRIVATE_TESTING_IVAR_FLAG = 4;
    const PUBLIC_IVAR_FLAG          = 2;
    const PUBLIC_TESTING_IVAR_FLAG  = 1;


    static function publicPoliteGetterMethod($key)
    {
        return 'get' . ucfirst($key);
    }

    static function privatePoliteGetterMethod($key)
    {
        return '_get' . ucfirst($key);
    }

    static function publicSetterMethod($key)
    {
        return 'set' . ucfirst($key);
    }

    static function privateSetterMethod($key)
    {
        return '_set' . ucfirst($key);
    }

    static function privateKey($key)
    {
        return '_' . $key;
    }

    static function publicTestingKey($key)
    {
        return 'is' . ucfirst($key);
    }

    static function privateTestingKey($key)
    {
        return '_is' . ucfirst($key);
    }

    static function canAccessFieldsDirectly($obj)
    {
        return in_array('canAccessFieldsDirectly',
                        get_class_methods($obj)) &&
            $obj->canAccessFieldsDirectly();
    }

    static function valueForKey($obj, $key)
    {
        // search accessor methods
        $key = $key->phpString();
        $flags = 0;
        $publicPoliteGetter = self::publicPoliteGetterMethod($key);
        $publicTesting = self::publicTestingKey($key);
        $privatePoliteGetter = self::privatePoliteGetterMethod($key);
        $private = self::privateKey($key);
        $privateTesting = self::privateTestingKey($key);
        foreach (get_class_methods($obj) as $meth) {
            switch ($meth) {
            case $publicPoliteGetter:
                return $obj->$publicPoliteGetter();
            case $key:
                $flags |= self::PUBLIC_GETTER_METHOD_FLAG;
                break;
            case $publicTesting:
                $flags |= self::PUBLIC_TESTING_METHOD_FLAG;
                break;
            case $privatePoliteGetter:
                $flags |= self::PRIVATE_POLITE_GETTER_METHOD_FLAG;
                break;
            case $private:
                $flags |= self::PRIVATE_METHOD_FLAG;
                break;
            case $privateTesting:
                $flags |= self::PRIVATE_TESTING_METHOD_FLAG;
                break;
            }
        }
        if ($flags >= self::PUBLIC_GETTER_METHOD_FLAG)
            return $obj->$key();
        elseif ($flags >= self::PUBLIC_TESTING_METHOD_FLAG)
            return $obj->$publicTesting();
        elseif ($flags >= self::PRIVATE_POLITE_GETTER_METHOD_FLAG)
            return $obj->$privatePoliteGetter();
        elseif ($flags >= self::PRIVATE_METHOD_FLAG)
            return $obj->$private();
        elseif ($flags >= self::PRIVATE_TESTING_METHOD_FLAG)
            return $obj->$privateTesting();

        // search instance variables
        if (self::canAccessFieldsDirectly($obj)) {
            $flags = 0;
            foreach (get_object_vars($obj) as $var => $val) {
                switch ($var) {
                case $private:
                    return $val; 
                case $privateTesting:
                    $flags |= self::PRIVATE_TESTING_IVAR_FLAG;
                    break;
                case $key:
                    $flags |= self::PUBLIC_IVAR_FLAG;
                    break;
                case $publicTesting:
                    $flags |= self::PUBLIC_TESTING_IVAR_FLAG;
                    break;
                }
            }
            if ($flags >= self::PRIVATE_TESTING_IVAR_FLAG)
                return $obj->$privateTesting;
            elseif ($flags >= self::PUBLIC_IVAR_FLAG)
                return $obj->$key;
            elseif ($flags >= self::PUBLIC_TESTING_IVAR_FLAG)
                return $obj->$publicTesting;
        }

        PhobKeyValueCodingUtility::handleQueryWithUnboundKey($obj, $key);
    }

    static function handleQueryWithUnboundKey($obj, $key)
    {
        self::throwValueForKeyException($obj, $key);
    }
    
    static function throwValueForKeyException($obj, $key)
    {
        $msg = 'This `' . get_class($obj) .
            '\' object does not have methods ' .
            self::publicPoliteGetterMethod($key) . '(), ' .
            $key . '(), ' .
            self::publicTestingKey($key) . '(), ' .
            self::privatePoliteGetterMethod($key) . '(), ' .
            self::privateKey($key) . '(), ' .
            self::privateTestingKey($key) . '()';
        if (self::canAccessFieldsDirectly($obj)) {
            $msg .= ', nor instance variables ' .
                self::privateKey($key) . ', ' .
                self::privateTestingKey($key) . ', ' . 
                $key . ', ' .
                self::publicTestingKey($key);
        }
        $msg .= '.';
        throw new PhobKeyValueCodingKeyNotFoundException($msg, $obj, $key);
    }

    static function setValueForKey($obj, $value, $key)
    {
        // search accessor methods
        $key = $key->phpString();
        $hasPrivateSetter = false;
        $publicSetter = self::publicSetterMethod($key);
        $privateSetter = self::privateSetterMethod($key);
        foreach (get_class_methods($obj) as $meth) {
            switch ($meth) {
            case $publicSetter:
                $obj->$publicSetter($value);
                return;
            case $privateSetter:
                $hasPrivateSetter = true;
                break;
            }
        }

        // search instance variables
        if (self::canAccessFieldsDirectly($obj)) {
            $flags = 0;
            $private = self::privateKey($key);
            $privateTesting = self::privateTestingKey($key);
            $publicTesting = self::publicTestingKey($key);
            foreach (get_object_vars($obj) as $var => $current) {
                switch ($var) {
                case $private:
                    $obj->$var = $value;
                    return;
                case $privateTesting:
                    $flags |= self::PRIVATE_TESTING_IVAR_FLAG;
                    break;
                case $key:
                    $flags |= self::PUBLIC_IVAR_FLAG;
                    break;
                case $publicTesting:
                    $flags |= self::PUBLIC_TESTING_IVAR_FLAG;
                    break;
                }
            }
            if ($flags >= self::PRIVATE_TESTING_IVAR_FLAG) {
                $obj->$privateTesting = $value;
                return;
            } elseif ($flags >= self::PUBLIC_IVAR_FLAG) {
                $obj->$key = $value;
                return;
            } elseif ($flags >= self::PUBLIC_TESTING_IVAR_FLAG) {
                $obj->$publicTesting = $value;
                return;
            }
        }

        PhobKeyValueCodingUtility::handlesetValueWithUnboundKey($obj, $value, $key);
    }

    static function handlesetValueWithUnboundKey($obj, $value, $key)
    {
        self::throwsetValueForKeyException($obj, $key);
    }

    static function throwsetValueForKeyException($obj, $key)
    {
        $msg = 'This `' . get_class($obj) .
            '\' object does not have methods ' .
            self::publicSetterMethod($key) . '(), ' .
            self::privateSetterMethod($key) . '()';
        if (self::canAccessFieldsDirectly($obj)) {
            $msg .= ', nor instance variables ' .
                self::privateKey($key) . '(), ' .
                self::privateTestingKey($key) . '(), ' .
                $key . ', ' .
                self::publicTestingKey($key);
        }
        $msg .= '.';
        throw new PhobKeyValueCodingKeyNotFoundException($msg, $obj, $key);
    }

}

class PhobKeyValueCodingUtility
{

    static function valueForKey($obj, $key)
    {
        if (is_object($obj))
            if (self::implementsKeyValueCoding($obj))
                return $obj->valueForKey($key);
            else
                return PhobKeyValueCodingDefaultImplementation::valueForKey($obj, $key);
        else
            self::throwKeyValueCodingNotSupportedException($obj);
    }

    static function setValueForKey($obj, $value, $key)
    {
        if (is_object($obj))
            if (self::implementsKeyValueCoding($obj))
                return $obj->setValueForKey($value, $key);
            else
                return PhobKeyValueCodingDefaultImplementation::setValueForKey($obj, $value, $key);
        else
            self::throwKeyValueCodingNotSupportedException($obj);
    }

    static function throwKeyValueCodingNotSupportedException($obj)
    {
        if (is_object($obj))
            $type = get_class($obj);
        else
            $type = gettype($obj);
        $msg = "PhobKeyValueCoding does not support `$type' object";
        throw new PhobKeyValueCodingNotSupportedException($obj, $msg);
    }

    static function handleQueryWithUnboundKey($obj, $key)
    {
        if (self::implementsErrorHandling($obj))
            $obj->handleQueryWithUnboundKey($key);
        else
            PhobKeyValueCodingDefaultImplementation::handleQueryWithUnboundKey($obj, $key);
    }

    static function handlesetValueForUnboundKey($obj, $value, $key)
    {
        if (self::implementsErrorHandling($obj))
            $obj->handlesetValueWithUnboundKey($value, $key);
        else
            PhobKeyValueCodingDefaultImplementation::handlesetValueWithUnboundKey($obj, $value, $key);
    }

    static function implementsKeyValueCoding($obj) {
        return in_array('PhobKeyValueCoding', class_implements($obj));
    }


    static function implementsErrorHandling($obj) {
        return in_array('PhobKeyValueCodingErrorHandling',
                         class_implements($obj));
    }

}


class PhobKeyValueCodingAdditionsDefaultImplementation
{

    static function valueForKeyPath($obj, $keyPath)
    {
        $keys = PhobKeyValueCodingAdditionsUtility::keysFromKeyPath($keyPath);
        $key = $keys->removeFirstObject();
        $value = PhobKeyValueCodingUtility::valueForKey($obj, $key);
        $restKeyPath = PhobKeyValueCodingAdditionsUtility::keyPathFromKeys($keys);
        if ($restKeyPath)
            return PhobKeyValueCodingAdditionsUtility::valueForKeyPath($value, $restKeyPath);
        else
            return $value;
    }

    static function setValueForKeyPath($obj, $value, $keyPath)
    {
        $keys = PhobKeyValueCodingAdditionsUtility::keysFromKeyPath($keyPath);
        $lastKey = array_pop($keys);
        foreach ($keys as $key)
            $obj = PhobKeyValueCodingUtility::valueForKey($obj, $key);
        PhobKeyValueCodingUtility::setValueForKey($obj, $value, $lastKey);
    }

}


class PhobKeyValueCodingAdditionsUtility extends PhobObject
{

    const KEY_PATH_SEPARATOR = '.';

    static function keyPathFromKeys($keys)
    {
        return $keys->componentsJoinedByString
            (PhobKeyValueCodingAdditionsUtility::KEY_PATH_SEPARATOR);
    }

    static function keysFromKeyPath($keyPath)
    {
        return $keyPath->componentsSeparatedByString
            (PhobKeyValueCodingAdditionsUtility::KEY_PATH_SEPARATOR); 
    }

    static function valueForKeyPath($obj, $keyPath)
    {
        if (is_object($obj))
            if (self::implementsKeyValueCodingAdditions($obj))
                return $obj->valueForKeyPath($key);
            else
                return PhobKeyValueCodingAdditionsDefaultImplementation::valueForKeyPath($obj, $keyPath);
        else
            PhobKeyValueCodingUtility::throwKeyValueCodingNotSupportedException($obj);
    }

    static function setValueForKeyPath($obj, $value, $keyPath)
    {
        if (is_object($obj))
            if (self::implementsKeyValueCodingAdditions($obj))
                return $obj->setValueForKeyPath($value, $keyPath);
            else
                return PhobKeyValueCodingAdditionsDefaultImplementation::setValueForKeyPath($obj, $value, $keyPath);
        else
            PhobKeyValueCodingUtility::throwKeyValueCodingNotSupportedException($obj);
    }

    static function implementsKeyValueCodingAdditions($obj) {
        return in_array('PhobKeyValueCodingAdditions', class_implements($obj));
    }

}

