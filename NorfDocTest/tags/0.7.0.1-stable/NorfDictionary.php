<?php

class NorfDictionary implements IteratorAggregate
{

    static function hash($obj)
    {
        if (is_null($obj))
            return '';
        elseif (is_string($obj) or is_int($obj))
            return $obj;
        elseif (is_object($obj))
            return spl_object_hash($obj);
        else
            throw new Exception('NorfDictionary::hash() does not support `'
                                . gettype($obj) . "'");
    }

    function __construct()
    {
        $this->_dict = array();
        $this->_keyDict = array();
    }

    function initWithDictionary($anDict)
    {
        $this->init();
        foreach ($anDict as $key => $obj)
            $this->setObjectForKey($obj, $key);
        return $this;
    }

    function initWithObjectForKey($obj, $key)
    {
        $this->init();
        $this->setObjectForKey($obj, $key);
        return $this;
    }

    function initWithObjectsForKeys($objs, $keys)
    {
        $this->init();
        for ($i = 0; $i < $objs->count(); $i++)
            $this->setObjectForKey($objs->objectAtIndex($i),
                                   $keys->objectAtIndex($i));
        return $this;
    }

    function initWithObjectsAndKeysInArray($array)
    {
        $this->init();
        for ($i = 0; $i < $array->count(); $i+=2) {
            $obj = $array->objectAtIndex($i);
            $key = $array->objectAtIndex($i+1);
            $this->setObjectForKey($obj, $key);
        }
        return $this;
    }

    function initWithPHPArrayNoCopy($array)
    {
        $this->init();
        $this->setPHPArrayNoCopy($array);
        return $this;
    }

    function count()
    {
        return count($this->_dict);
    }

    function isEmpty()
    {
        return empty($this->_dict);
    }

    function isEqualToDictionary($dict)
    {
        if ($this === $dict or $this->_dict === $dict)
            return true;
        elseif ($this->count() != $dict->count())
            return false;
        else { 
            foreach ($this as $key => $obj) {
                $obj2 = $dict->objectForKey($key);
                if ($obj != $obj2)
                    return false;
            }
            return true;
        }
    }

    function allKeys()
    {
        $keys = new NorfArray();
        $keys->addObjectsFromPHPArray(array_values($this->_keyDict));
        return $keys;
    }

    function allKeysForObject($anObject)
    {
        $keys = new NorfArray();
        foreach ($this as $key => $obj) {
            if ($obj == $anObject)
                $keys->addObject($key);
        }
        return $keys;
    }

    function allValues()
    {
        $keys = new NorfArray();
        $keys->addObjectsFromPHPArray(array_values($this->_dict));
        return $keys;
    }

    function getIterator()
    {
        return $this->dictionaryEnumerator();
    }

    function dictionaryEnumerator()
    {
        return new NorfDictionaryEnumerator($this);
    }

    function keyEnumerator()
    {
        return $this->allKeys()->objectEnumerator();
    }

    function objectEnumerator()
    {
        return $this->allValues()->objectEnumerator();
    }

    function gathererToCollect()
    {
        return new NorfCollectionGatherer(proc('$x,$y: return NorfDictionary::dictionaryWithObjectsAndKeysInArray($y);'), $this);
    }

    function objectForKey($key)
    {
        if ($this->containsKey($key))
            return $this->_dict[self::hash($key)];
        else
            return null;
    }

    function objectForKeyWithDefaultValue($key, $default, $set=false)
    {
        if ($this->containsKey($key))
            return $this->_dict[self::hash($key)];
        else {
            if ($set)
                $this->_dict[self::hash($key)] = $default;
            return $default;
        }
    }

    function objectForKeyBySettingDefaultValue($key, $default)
    {
        return $this->objectForKeyWithDefaultValue($key, $default, true);
    }

    function objectForKeyWithDefaultValueProcedure($key, $proc, $set=false)
    {
        if ($this->containsKey($key))
            return $this->_dict[self::hash($key)];
        else {
            $default = $proc->apply();
            if ($set)
                $this->_dict[self::hash($key)] = $default;
            return $default;
        }
    }

    function objectForKeyBySettingDefaultValueWithProcedure($key, $proc)
    {
        return $this->objectForKeyWithDefaultValueProcedure($key, $proc, true);
    }

    function objectsForKeys($keys, $marker=null)
    {
        $objs = new NorfArray();
        foreach ($keys as $key)
            $objs->addObject($this->objectForKey($key, $marker));
        return $objs;
    }

    function containsKey($key)
    {
        return array_key_exists(self::hash($key), $this->_dict);
    }

    function containsValue($value)
    {
        foreach ($this->_dict as $baseKey => $obj) {
            if ($obj == $value)
                return true;
        }
        return false;
    }

    function setObjectForKey($obj, $key)
    {
        $baseKey = self::hash($key);
        $this->_dict[$baseKey] = $obj;
        $this->_keyDict[$baseKey] = $key;
    }

    function addEntriesFromDictionary($dict)
    {
        foreach ($dict as $key => $obj)
            $this->setObjectForKey($obj, $key);
    }

    function setDictionary($dict)
    {
        $temp = $this->klass()->dictionaryWithDictionary($dict);
        $this->setDictionaryNoCopy($temp);
    }

    function setDictionaryNoCopy($dict)
    {
        $this->_dict = $dict->dict;
    }

    function setPHPArray($array)
    {
        $dict = array();
        foreach ($array as $key => $obj)
            $dict[$key] = $obj;
        $this->_dict = $dict;
    }

    function setPHPArrayNoCopy($array)
    {
        $this->_dict = $array;
    }

    function removeObjectForKey($key)
    {
        unset($this->_dict[$key]);
    }

    function removeObjectsForKeys($keys)
    {
        foreach ($keys as $key)
            unset($this->_dict[$key]);
    }

    function removeAllObjects()
    {
        $this->_dict = array();
    }

    function valueForKey($key)
    {
        if ($this->containsKey($key))
            return $this->objectForKey($key);
        else
            return NorfKeyValueCodingDefaultImplementation::valueForKey($this, $key);
    }

    function takeValueForKey($value, $key)
    {
        if ($this->containsKey($key))
            $this->setObjectForKey($value, $key);
        else
            NorfKeyValueCodingDefaultImplementation::takeValueForKey($this, $value, $key);
    }

    function __toString()
    {
        $s = '{ ';
        $assocs = new NorfArray();
        foreach ($this->dictionaryEnumerator() as $key => $obj) {
            if ($this === $key)
                $keyDesc = '$this';
            else
                $keyDesc = (string)$key;
            if ($this === $obj)
                $objDesc = '$this';
            else
                $objDesc = (string)$obj;
            $s .= $keyDesc . "=" . $objDesc;
            $assocs->addObject($s);
        }
        $s .= $assocs->componentsJoinedByString(', ') . '}';
        return $s;
    }

}


class NorfDictionaryEnumerator extends NorfAssociationGeneratorIterator
{

    function __construct($dict)
    {
        parent::__construct();
        $this->_dict = $dict;
        $this->_keyEnum = $dict->keyEnumerator();
    }

    function hasNextObject()
    {
        return $this->_keyEnum->hasNextObject();
    }

    function nextKey()
    {
        return $this->_keyEnum->nextObject();
    }

    function nextObjectForKey($key)
    {
        return $this->_dict->objectForKey($key);
    }

}

