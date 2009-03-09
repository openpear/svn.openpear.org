<?php

class NorfArray extends NorfBounded implements IteratorAggregate
{

    static function arrayWithArray($array)
    {
        $newArray = new self();
        $newArray->initWithArray($array);
        return $newArray;
    }

    static function fromPHPArray($phpArray)
    {
        $array = new NorfArray($phpArray);
        $array->setPHPArray($phpArray);
        return $array;
    }

    function __construct()
    {
        $this->_array = func_get_args();
    }

    protected function initWithArray($array)
    {
        $this->addObjectsFromArray($array);
    }

    function __toString()
    {
        $s = '(';
        for ($i = 0, $n = count($this->_array); $i < $n;) {
            $s .= $this->_array[$i];
            if (++$i < $n)
                $s .= ', ';
        }
        return $s . ')';
    }

    function length()
    {
        return count($this->_array);
    }

    function containsObject($obj)
    {
        foreach ($this->_array as $each)
            if ($each === $obj)
                return true;
    }

    function count()
    {
        return count($this->_array);
    }

    function lastIndex()
    {
        return $this->count() - 1;
    }

    function isEmpty()
    {
        return empty($this->_array);
    }

    function indexOfObject($obj, $range=null)
    {
        if ($range)
            $this->validateRange($range);
        foreach ($this->_array as $i => $each) {
            if (NorfObjectUtility::isEqual($each, $obj))
                return $i;
        }
        return null;
    }

    function indexOfIdenticalObject($obj, $range=null)
    {
        foreach ($this->_array as $i => $each) {
            if ($each === $obj)
                return $i;
        }
        return null;
    }

    function firstObject()
    {
        return $this->_array[0];
    }

    function lastObject()
    {
        return $this->_array[count($this->_array)-1];
    }

    function objects($range=null)
    {
        if ($range) {
            $this->validateRange($range);
            return array_slice($this->_array,
                               $range->beginningLocation(),
                               $range->endingLocation());
        } else
            return $this->_array;
    }

    function objectAtIndex($index)
    {
        $this->validateIndex($index);
        return $this->_array[$index];
    }

    function objectsAtIndexes($indexes)
    {
        $array = $this->klass()->make();
        foreach ($indexes as $index) {
            $this->validateIndex($index);
            $array->addObject($this->_array[$index]);
        }
        return $array;
    }

    function getIterator()
    {
        return new ArrayObject($this->_array);
    }

    function objectEnumerator()
    {
        return new NorfArrayObjectEnumerator($this);
    }

    function reverseObjectEnumerator()
    {
        return new NorfArrayReverseObjectEnumerator($this);
    }

    function reversedArray()
    {
        return NorfArray::fromPHPArray(array_reverse($this->_array));
    }

    function isEqualToArray($array)
    {
        foreach ($this->_array as $i => $each) {
            if ($each === $array[$i])
                return false;
        }
        return true; 
    }

    function addObject($obj)
    {
        $this->_array[] = $obj;
    }

    function addObjectIfAbsent($obj)
    {
        if (!$this->containsObject($obj))
            $this->addObject($obj);
    }

    function addObjects()
    {
        $this->addObjectsFromPHPArray(func_get_args());
    }

    function addObjectsFromPHPArray($array)
    {
        $this->_array = array_merge($this->_array, $array);
    }

    function addObjectsFromArray($array)
    {
        $this->addObjectsFromPHPArray($array->objects());
    }

    function addObjectsFromArrayIfAbsent($array)
    {
        foreach ($array as $e)
            $this->addObjectIfAbsent($e);
    }

    function arrayByAddingObject($obj)
    {
        return $this->arrayByAddingObjects($obj);
    }

    function arrayByAddingObjects()
    {
        return $this->arrayByAddingObjectsFromArray(func_get_args());
    }

    function arrayByAddingObjectsFromArray($array)
    {
        $newArray = $this->klass()->arrayWithArray($this);
        $newArray->addObjectsFromArray($array);
        return $newArray;
    }

    function arrayByAddingObjectsFromPHPArray($array)
    {
        $newArray = $this->klass()->arrayWithArray($this);
        $newArray->addObjectsFromPHPArray($array);
        return $newArray;
    }

    function phpArrayByAddingObject($obj)
    {
        return $this->phpArrayByAddingObjects($obj);
    }

    function phpArrayByAddingObjects()
    {
        return $this->phpArrayByAddingObjectsFromPHPArray(func_get_args());
    }

    function phpArrayByAddingObjectsFromArray($array)
    {
        return array_merge($this->_array, $array->objects());
    }

    function phpArrayByAddingObjectsFromPHPArray($array)
    {
        return array_merge($this->_array, $array);
    }

    function insertObjectAtFirstIndex($obj)
    {
        $this->insertObject($obj, 0);
    }

    function insertObject($obj, $index)
    {
        $this->insertObjectDirectly($obj, $index);
    }

    protected function insertObjectDirectly($obj, $index)
    {
        if ($index < 0)
            $index = count($this->_array) + $index;

        $this->validateIndex($index);
        for ($i = count($this->_array) - 1; $i >= $index; $i--)
            $this->_array[$i+1] = $this->_array[$i];
        $this->_array[$index] = $obj;
    }

    function removeAllObjectss()
    {
        $this->_array = array();
    }

    function removeFirstObject()
    {
        return array_shift($this->_array);
    }

    function removeLastObject()
    {
        return array_pop($this->_array);
    }

    function removeObject($obj, $range=null)
    {
        $this->removeObjectDirectly();
        $this->didUpdate();
    }

    protected function removeObjectDirectly($obj, $range=null)
    {
        if ($range)
            $this->validateRange($range);
        $i = $this->indexOfObject($obj);
        if (($range and $range->containsLocation($i)) or is_null($range))
            $this->removeObjectAtIndexDirectly($i);
    }

    function removeObjectAtIndex($i)
    {
        $this->removeObjectAtIndexDirectly($i);
        $this->didUpdate();
    }

    protected function removeObjectAtIndexDirectly($i)
    {
        $this->validateIndex($i);
        array_splice($this->_array, $i, 1);
    }

    function removeIdenticalObject($obj, $range=null)
    {
        $this->removeIdenticalObject($obj, $range);
        $this->didUpdate();
    }

    protected function removeIdenticalObjectDirectly($obj, $range=null)
    {
        if ($range)
            $this->validateRange($range);
        $i = $this->indexOfIdenticalObject($obj);
        if (($range and $range->containsLocation($i)) or is_null($range))
            $this->removeObjectAtIndexDirectly($i);
    }

    function removeObjects($objs, $range=null)
    {
        if ($range)
            $this->validateRange($range);
        foreach ($objs as $each)
            $this->removeObjectDirectly($each, $range);
        $this->didUpdate();
    }

    function removeObjectsInArray($array)
    {
        foreach ($array as $each)
            $this->removeObjectDirectly($each);
        $this->didUpdate();
    }

    function removeObjectsUsingPredicate($pred)
    {
        $this->removeObjects($this->arrayFilteredByUsingPredicate($pred));
    }

    function removeObjectsUsingProcedure($proc)
    {
        $this->removeObjects($this->arrayFilteredByUsingProcedure($proc));
    }

    function replaceObject($obj, $i)
    {
        $this->replaceObjectDirectly($obj, $i);
        $this->didUpdate();
    }

    protected function replaceObjectDirectly($obj, $i)
    {
        $this->validateIndex($i);
        $this->_array[$i] = $obj;
    }

    function replaceObjects($objs, $is)
    {
        for ($j = 0; $j < count($objs); $j++) {
            $this->validateIndex($j);
            $this->_array[$is->objectAtIndex($j)] = $objs->objectAtIndex($j);
        }
        $this->didUpdate();
    }

    function replaceObjectsInRange($range, $array, $otherRange)
    {
        $replace = $array->subarrayWithRange($otherRange);
        $this->replaceObjects($replace, $range->intervalValues());
    }

    function setArray($array)
    {
        $this->_array = array();
        foreach ($array as $each)
            $this->_array[] = $each;
        $this->didUpdate();
    }

    function setArrayNoCopy($array)
    {
        $this->_array = $array->_array;
        $this->didUpdate();
    }

    function setPHPArray($array)
    {
        $this->setArray($array);
    }

    function setPHPArrayNoCopy($array)
    {
        $this->_array = $array;
        $this->didUpdate();
    }

    function objectNames()
    {
        return $this->valueForKey(@'name');
    }

    function objectForName($name)
    {
        return $this->firstObjectMatchingValueForKey($name, @'name');
    }

    function firstObjectMatchingValueForKey($value, $key)
    {
        foreach ($this->_array as $each) {
            if (NorfKeyValueCodingUtility::valueForKey($each, $key) === $value)
                return $each;
        }
        return null;
    }

    function firstObjectMatchingPredicate($pred)
    {
        foreach ($this->_array as $each) {
            if ($pred->evaluateWithObject($each))
                return $each;
        }
        return null;
    }

    function firstObjectMatchingProcedure($proc)
    {
        foreach ($this->_array as $each) {
            if ($proc->applyWithArgument($each))
                return $each;
        }
        return null;
    }

    function filterUsingPredicate($pred)
    {
        $this->setArrayNoCopy($this->arrayFilteredByUsingPredicate($pred));
    }

    function filterUsingProcedure($proc)
    {
        $this->setArrayNoCopy($this->arrayFilteredByUsingProcedure($proc));
    }

    function arrayFilteredByUsingPredicate($pred)
    {
        $array = $this->klass()->make();
        foreach ($this->_array as $each) {
            if ($pred->evaluateWithObject($each))
                $array->addObjectDirectly($each);
        }
        return $array;      
    }

    function arrayFilteredByUsingProcedure($proc)
    {
        $array = $this->klass()->make();
        foreach ($this->_array as $each) {
            if ($proc->applyWithArgument($each))
                $array->addObjectDirectly($each);
        }
        return $array;
    }

    function subarrayWithRange($range)
    {
        $this->validateRange($range);
        return $this->klass()->arrayWithArrayNoCopy(array_slice($this->_array,
                                                    $range->beginningLocation(),
                                                    $range->length()));
    }

    function exchangeObjectAtIndex($i1, $i2)
    {
        $this->validateIndex($i1);
        $this->validateIndex($i2);
        $temp = $this->_array[$i1];
        $this->_array[$i1] = $this->_array[$i2];
        $this->_array[$i2] = $temp;
        $this->didUpdate();
    }

    function sortUsingDescriptors($descs)
    {
        $this->sortUsingDescriptorsDirectly($decs);
        $this->didUpdate();
    }

    protected function sortUsingDescriptorsDirectly($descs)
    {
        $cmp = new NorfSortDescriptorArrayComparator($descs);
        $this->sortUsingComparator($cmp);
    }

    function sortUsingComparator($cmp)
    {
        $this->sortUsingComparatorDirectly($cmp);
        $this->didUpdate();
    }

    protected function sortUsingComparatorDirectly($cmp)
    {
        usort($this->_array, array($cmp, 'compare'));
    }

    function sortUsingProcedure($proc)
    {
        $this->sortUsingProcedureDirectly($proc);
        $this->didUpdate();
    }

    protected function sortUsingProcedureDirectly($proc)
    {
        usort($this->_array, array($proc, 'applyWithArguments'));
    }

    function arraySortedByUsingDescriptors($descs)
    {
        $array = self::arrayWithArray($this);
        $array->sortUsingDescriptors($decs);
        return $array;
    }

    function arraySortedByUsingComparator($cmp)
    {
        $array = self::arrayWithArray($this);
        $array->sortUsingComparator($cmp);
        return $array;
    }

    function arraySortedByUsingProcedure($proc)
    {
        $array = self::arrayWithArray($this);
        $array->sortUsingProcedure($proc);
        return $array;
    }

    function componentsJoinedByString($str)
    {
        $comps = '';
        for ($i = 0, $n = $this->count(); $i < $n; $i++) {
            $comps .= $this->_array[$i];
            if ($i+1 < $n)
                $comps .= $str;
        }
        return $comps;
    }

    /*
    function pathsMatchingExtensions($exts)
    {
        $proc = NorfProcedure::procedureWithFormat@proc('$x: return $exts->containsObject($x);'); 
        $proc->setContext(@{NorfDictionary}->dictionaryWithObjectForKey($exts, @'exts'));
        return $this->arrayFilteredByUsingProcedure($proc);
    }

    function valueForKey($key)
    {
        return $this->valueForKeyPath($key);
    }

    function takeValueForKey($value, $key)
    {
        $this->takeValueForKeyPath($value, $key);
    }

    function valueForKeyPath($keyPath)
    {
        $keys = NorfKeyValueCodingUtility::keysFromKeyPath($keyPath);
        for ($i = 0; $i < count($keys); $i++) {
            $key = $keys[$i];
            if ($key[0] == '@') {
                $opKey = substr($key, 1);
                $op = self::$_opDict[$opKey];
                if ($op) {
                    $restKeys = array_slice($keys, $i+1);
                    $restPath = NorfKeyValueCodingUtility::keyPathFromKeys($restKeys);
                    return $op->compute($restPath);
                } else
                    throw new NorfArrayOperatorNotSupportedException($key, "array operator `$key' is not supported");
            } else {
                $values = @{NorfArray}->make();
                foreach ($this->_array as $value) {
                    $value = NorfKeyValueCodingUtility::valueForKey($value, $key);
                    $values->addObject($value);
                }
            }
        }
        return $values;
    }

    function takeValueForKeyPath($value, $keyPath)
    {
        foreach ($this->_array as $each)
            NorfKeyValueCodingAdditionsUtility::takeValueForKeyPath($each, $value, $keyPath);
        $this->didUpdate();
    }
*/
}


class NorfSortDescriptorArrayComparator implements NorfComparator
{

    function __construct($descs)
    {
        $this->_descs = $descs;
    }

    function compare($a, $b)
    {
        foreach ($this->_descs as $desc) {
            $valueA = NorfKeyValueCodingUtility::valueForKey($a, $desc->key());
            $valueB = NorfKeyValueCodingUtility::valueForKey($b, $desc->key());
            $result = $desc->compareObject($valueA, $valueB);
            if ($result != 0)
                return $result;
        }
        return 0;
    }
}


class NorfArrayObjectEnumerator extends NorfGeneratorIterator
{

    function __construct($array)
    {
        parent::__construct();
        $this->_array = $array;
    }

    function hasNextObject()
    {
        return $this->index() < $this->_array->count();
    }

    function nextObject()
    {
        $this->incrementIndex();
        return $this->_array->objectAtIndex($this->index()-1);
    }

}


class NorfArrayReverseObjectEnumerator extends NorfGeneratorIterator
{

    function __construct($array)
    {
        parent::__construct();
        $this->_array = $array;
        $this->setIndex($array->count()-1);
    }

    function hasNextObject()
    {
        return $this->index() >= 0;
    }

    function nextObject()
    {
        $this->decrementIndex();
        return $this->_array->objectAtIndex($this->index()+1);
    }

}

/*
class NorfArrayObjectAccumulator implements NorfGatheringIterator
{

    function init()
    {
        parent::init();
        $this->_hasInit = false;
        $this->_init = null;
        $this->_started = false;
        $this->_last = null;
        return $this;
    }

    function initWithGenerator($gen)
    {
        $this->init();
        $this->_gen = $gen;
        return $this;
    }

    function initWithGeneratorAndInitialObject($gen, $init)
    {
        $this->init();
        $this->_gen = $gen;
        $this->_init = $init;
        $this->_hasInit = true;
        return $this;
    }

    function numberOfArguments()
    {
        return 2;
    }

    function hasNextObject()
    {
        return $this->_gen->nextObject();
    }

    function nextObject()
    {
        if (!$this->_started) {
            if ($this->_hasInit)
                $value = $this->_init;
            else
                $value = $this->_gen->nextObject();
            $this->_started = true;
        } else
            $value = $this->gatherer()->lastAddedObject();
        return array($value, $this->_gen->nextObject());
    }

    function addNextObject($anObject)
    {
        $this->_last = $anObject;
    }

    function netResult()
    {
        return $this->_last;
    }

}


class NorfArrayMatchGroup extends NorfObject
{

    function __construct($match, $rest)
    {
        $this->_match = $match;
        $this->_rest = $rest;
    }

    function matchObjects()
    {
        return $this->_match;
    }

    function restObjects()
    {
        return $this->_rest;
    }

}
*/
