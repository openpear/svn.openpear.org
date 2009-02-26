<?php

class NorfStringScanner
{

    const SPACES_SKIP = " \r\n\t";
    const ALPHANUM_PATTERN    = '/\G\w+/';
    const WS_PATTERN          = '/\G\s+/';
    const WS_NL_PATTERN       = '/\G\s+/m';

    function __construct($str, $lc=0, $skip=null)
    {
        $this->_str = $str;
        $this->_strlen = strlen($str);
        $this->_lc = $lc;
        $this->_end = false;
        $this->_matched = null;
        $this->setCharactersToBeSkipped($skip);
    }

    function string()
    {
        return $this->_str;
    }

    function location()
    {
        return $this->_lc;
    }

    function setLocation($lc)
    {
        $this->_lc = $lc;
    }

    function lineNumber()
    {
        if ($this->_lc == 0)
            return 1;
        else
            return substr_count($this->_str, "\n", 0, $this->_lc) + 1;
    }

    function characterColumnNumber()
    {
        $c = null;
        for ($n = -1, $i = $this->_lc; $c !== "\n" and $i > 0; $n++, $i--)
            $c = $this->_str[$i];
        return $n;
    }

    function charactersToBeSkipped()
    {
        return $this->_skip;
    }

    function setCharactersToBeSkipped($chars)
    {
        $this->_skip = $chars;
    }

    function isAtEndOfString()
    {
        return $this->_lc >= $this->_strlen;
    }

    function isNextLocationAtEndOfString()
    {
        return $this->nextLocation() === null;
    }

    function isAtBeginOfLine()
    {
        return $this->_str[$this->_lc] == "\n";
    }

    function forwardString($len)
    {
        if ($this->isAtEndOfString())
            return null;
        else {
            $lc = $this->nextLocation();
            return substr($this->_str, $lc, $len);
        }
    }

    function restString()
    {
        if ($this->isAtEndOfString())
            return null;
        else
            return substr($this->_str, $this->_lc);
    }

    function restStringLength()
    {
        if ($this->isAtEndOfString())
            return 0;
        else
            return $this->_strlen - $this->_lc;
    }

    function nextLocation($lc=null)
    {
        if (is_null($lc))
            $lc = $this->_lc;
        if (!$this->_skip)
            return $lc;

        $count = 0;
        $str = $this->_str;
        $end = $this->_strlen;
        $skip = $this->_skip;
        $skipLen = strlen($this->_skip);
        while ($lc < $end) {
            $match = false;
            for ($i = 0; $i < $skipLen; $i++) {
                if ($str[$lc] == $skip[$i]) {
                    $match = true;
                    break;
                }
            }
            if ($match)
                $lc++;
            else
                return $lc;
        }
        if ($lc < $end)
            return $lc;
        else
            return null;
    }

    function _validateAtEndOfString()
    {
        if ($this->isAtEndOfString())
            throw new NorfStringScannerAtEndException();
    }

    function scanCharacter()
    {
        return $this->scanStringOfLength(1);
    }

    function scanStringOfLength($len)
    {
        $this->_validateAtEndOfString();
        if (($temp = $this->nextLocation()) === null)
            return null;
        $str = substr($this->_str, $temp, $len);
        $this->_lc = $temp + $len;
        return $str;
    }

    function scanString($str, $advance=true)
    {
        $this->_validateAtEndOfString();
        if (($temp = $this->nextLocation()) === null)
            return null;
        if ($this->_str[$temp] != $str[0])
            return null;

        $len = strlen($str);
        $scan = substr($this->_str, $temp, $len);
        if ($scan == $str) {
            if ($advance)
                $this->_lc = $temp + $len;
            return true;
        } else
            return false;
    }

    function scanUpToString($str, $advance=true)
    {
        $this->_validateAtEndOfString();
        $pos = strpos($this->_str, $str, $this->_lc);
        if ($pos === false)
            return NULL;
        else {
            $scan = substr($this->_str, $this->_lc, $pos - $this->_lc);
            if ($advance)
                $this->_lc = $pos + strlen($str);
            return $scan;
        }
    }

    function scanStringNoAdvance($str)
    {
        return $this->scanString($str, false);
    }

    function scanUpToStringNoAdvance($str)
    {
        return $this->scanUpToString($str, false);
    }
 
    function scanPattern($pattern, $advance=true)
    {
        $this->_validateAtEndOfString();
        if (($temp = $this->nextLocation()) === null)
            return null;

        $count = preg_match($pattern, $this->_str, $matches,
                   PREG_OFFSET_CAPTURE, $temp);
        if ($count > 0) {
            if ($advance)
                $this->_lc = strlen($matches[0][0]) + $matches[0][1];
            return $matches[0][0];
        } else
            return null;
    }

    function scanPatternNoAdvance($pattern)
    {
        return $this->scanPattern($pattern, false);
    }

    function scanAlphanumerics()
    {
        return $this->scanPattern(self::ALPHANUM_PATTERN);
    }

    function scanWhitespaces()
    {
        return $this->scanPattern(self::WS_PATTERN);
    }

    function scanWhitespacesAndNewLines()
    {
        return $this->scanPattern(self::WS_NL_PATTERN);
    }

    function scanNewLines()
    {
        return $this->scanPattern("/\G\n/m");
    }

    function scanUpToNewLine()
    {
        return $this->scanUpToString("\n");
    }

}


class NorfStringScannerAtEndException extends Exception
{
    protected $message = 'reached end of string';
}

