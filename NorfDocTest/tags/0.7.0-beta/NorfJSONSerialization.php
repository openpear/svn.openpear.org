<?php

class NorfJSONSerialization
{

    static function objectFromJSON($s, $norf=true)
    {
        $parser = new NorfJSONParser($s, $norf);
        $parser->parse();
        return $parser->object();
    }

    static function PHPObjectFromJSON($s)
    {
        return self::objectFromJSON($s, false);
    }

    static function JSONFromString($s)
    {
        return '"' . NorfStringUtilities::stringEscapedDoubleQuotes . '"';
    }

    static function compareValues($left, $right)
    {
        if (NorfClassUtilities::isKindOfClass($left, 'NorfArray'))
            return self::isEqualToArray($left, $right);
        else if (NorfClassUtilities::isKindOfClass($right, 'NorfArray'))
            return self::isEqualToArray($right, $left);
        else if (NorfClassUtilities::isKindOfClass($left, 'NorfDictionary'))
            return self::isEqualToDictionary($left, $right);
        else if (NorfClassUtilities::isKindOfClass($right, 'NorfDictionary'))
            return self::isEqualToDictionary($right, $left);
        else
            return $left === $right;
    }

    private static function isEqualToArray($left, $right)
    {
        if (NorfClassUtilities::isKindOfClass($right, 'NorfArray'))
            return $left->isEqualToArray($right);
        else if (is_array($right)) {
            if ($left->count() !== count($right))
                return false;
            for ($i = 0, $n = $left->count(); $i < $n; $i++) {
                if (!self::compareValues($left->objectAtIndex($i),
                                         $right[$i]))
                    return false;
            }
            return true;
        } else
            return false;
    }
   
    private static function isEqualToDictionary($left, $right)
    {
        if (NorfClassUtilities::isKindOfClass($right, 'NorfDictionary'))
            return $left->isEqualToDictionary($right);
        else if (is_array($right)) {
            if ($left->count() !== count($right))
                return false;
            foreach ($left as $key => $value) {
                if (!(array_key_exists($key, $right) &&
                      self::compareValues($value, $right[$key])))
                    return false;
            }
            return true;
        } else
            return false;
    }

}


class NorfJSONParser
{

    private $_s;
    private $_norf;
    private $_obj;
    private $_sc;

    function __construct($s, $norf=true)
    {
        $this->_s = $s;
        $this->_norf = $norf;
    }

    function object()
    {
        return $this->_obj;
    }

    function parse()
    {
        $this->_sc = new NorfJSONScanner($this->_s);
        $this->_obj = $this->parseJSONValue();
    }

    function parseJSONValue()
    {
        if ($this->_sc->scanString('['))
            return $this->parseJSONArray();
        else if ($this->_sc->scanString('{'))
            return $this->parseJSONObject();
        else if ($str = $this->_sc->scanLiteralString())
            return $str;
        else if (($val = $this->_sc->scanNumber()) !== null)
            return $val;
        else if ($this->_sc->scanString('true'))
            return true;
        else if ($this->_sc->scanString('false'))
            return false;
        else if ($this->_sc->scanString('null'))
            return null;
        else
            $this->throwParseError('parse error at column ' .
                                   $this->_sc->nextLocation() .
                                   ' -- `' . $this->_sc->forwardstring(10) .
                                   '...\'');
    }

    function throwParseError($msg)
    {
        throw new NorfJSONParseError($this->_s, $this->_sc->nextLocation(),
                                     $msg);
    }

    function parseJSONARray()
    {
        $values = new NorfArray();
        while (!$this->_sc->scanString(']')) {
            $value = $this->parseJSONValue();
            $values->addObject($value);
            if (!$this->_sc->scanString(',') &&
                !$this->_sc->scanStringNoAdvance(']'))
                $this->throwParseError("expected ',' or ']'");
        }
        if ($this->_norf)
            return $values;
        else
            return $values->objects();
    }

    function parseJSONObject()
    {
        $dict = new NorfDictionary();
        while (!$this->_sc->scanString('}')) {
            $key = $this->parseJSONValue();
            if (!$this->_sc->scanString(':'))
                $thsi->throwParseError("expected ':'");

            $value = $this->parseJSONValue();
            if (!$this->_sc->scanString(',') &&
                !$this->_sc->scanStringNoAdvance('}'))
                $this->throwParseError("expected ',' or '}'");

            $dict->setObjectForKey($value, $key);
        }

        if ($this->_norf)
            return $dict;
        else {
            $object = new stdClass();
            foreach ($dict as $key => $value)
                $object->$key = $value;
            return $object;
        }
    }

}


class NorfJSONScanner extends NorfStringScanner
{

    const NUM_PATTERN = '/\G-?([1-9][0-9]*|[0-9])(\.[0-9]+)?([eE][+-]?[0-9]+)?/';

    function __construct($s, $lc=0)
    {
        parent::__construct($s, $lc, NorfStringScanner::SPACES_SKIP);
    }

    function scanLiteralString()
    {
        $this->_validateAtEndOfString();
        $begin = $this->nextLocation();
        $quote = $this->_str[$begin];
        if ($quote != '"')
            return null;

        $begin++;
        $s = $this->_str;
        $escape = false;
        $literal = '';
        for ($temp = $begin; $temp < $this->_strlen; $temp++) {
            $c = $s[$temp];
            if ($c === '\\') {
                if ($escape) {
                    $escape = false;
                    $literal .= '\\';
                } else
                    $escape = true;
            } elseif ($c === $quote) {
                if ($escape) {
                    $escape = false;
                    $literal .= $quote;
                } else {
                    $this->_lc = $temp + 1;
                    return $literal;
                }
            } else {
                if ($escape) {
                    $escape = false;
                    $literal .= '\\';
                }
                $literal .= $c;
            }
        }
        throw new NorfJSONParseError($s, $temp, 'reached end of string');
    }

    function scanNumber()
    {
        if (($match = $this->scanPattern(self::NUM_PATTERN)) !== null) {
            if (preg_match('/[\.eE]/', $match))
                return floatval($match);
            else
                return intval($match);
        } else
            return null;
    }


}

class NorfJSONParseError extends Exception
{

    private $_string;
    private $_lc;

    function __construct($string, $lc, $msg)
    {
        parent::__construct($msg);
        $this->_string = $string;
        $this->_lc = $lc;
    }

    function string()
    {
        return $this->_string;
    }

    function location()
    {
        return $this->_lc;
    }

}

