<?php

function NorfDocTestHasPrefix($str, $sub, $ci=false) {
    if ($ci) {
        $str = strtoupper($str);
        $sub = strtoupper($sub);
    }
    return substr($str, 0, strlen($sub)) === $sub;
}

function NorfDocTestHasSuffix($str, $sub, $ci=false) {
    if ($ci) {
        $str = strtoupper($str);
        $sub = strtoupper($sub);
    }
    return substr($str, strlen($str) - strlen($sub)) === $sub;
}

function NorfDocTestStringEscapedQuotes($str, $quote, $char='\\')
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

function NorfDocTestStringEscapedSingleQuotes($str)
{
    return NorfDocTestStringEscapedQuotes($str, "'");
}

function NorfDocTestStringEscapedDoubleQuotes($str)
{
    return NorfDocTestSstringEscapedQuotes($str, '"');
}

function NorfDocTestReplacePathExtension($path, $ext)
{
    $i = strrpos($path, '.');
    $base = substr($path, 0, $i); 
    return $base . '.' . $ext;
}

