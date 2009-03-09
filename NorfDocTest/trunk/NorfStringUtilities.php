<?php

class NorfStringUtilities
{

    static function hasPrefix($str, $sub, $ci=false) {
        if ($ci) {
            $str = strtoupper($str);
            $sub = strtoupper($sub);
        }
        return substr($str, 0, strlen($sub)) === $sub;
    }

    static function hasSuffix($str, $sub, $ci=false) {
        if ($ci) {
            $str = strtoupper($str);
            $sub = strtoupper($sub);
        }
        return substr($str, strlen($str) - strlen($sub)) === $sub;
    }

    static function stringEscapedQuotes($str, $quote, $char='\\')
    {
        $escaped = '';
        $escape = false;
        for ($i = 0; $i < strlen($str); $i++) {
            $c = $str[$i];
            if ($c === $quote) {
                if ($escape) {
                    $escaped .= $char;
                    $escape = false;
                }
                $escaped .= $char . $quote;
            } elseif ($c === $char) {
                $escape = !$escape;
                $escaped .= $c;
            } else
                $escaped .= $c;
        }
        return $escaped;
    }

    static function stringEscapedSingleQuotes($str)
    {
        return self::stringEscapedQuotes($str, "'");
    }

    static function stringEscapedDoubleQuotes($str)
    {
        return self::stringEscapedQuotes($str, '"');
    }

}

