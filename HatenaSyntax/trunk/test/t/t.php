<?php
include_once dirname(__FILE__) . '/lime.php';

foreach (array('/../../code') as $path) {
     set_include_path(dirname(__FILE__) . $path . PATH_SEPARATOR . get_include_path());
}

include_once 'HatenaSyntax.php';
/*
function __autoload($klass)
{
    include_once str_replace('_', '/', $klass) . '.php';
}*/

function token($token)
{
    return PEG_Token::get($token);
}

function context($s)
{
    return new PEG_Context($s);
}