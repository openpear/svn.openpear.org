<?php
include_once dirname(__FILE__) . '/default.php';
include_once dirname(__FILE__) . '/lime.php';

set_include_path(dirname(__FILE__) . '/../../code' . PATH_SEPARATOR . get_include_path());

function context($s)
{
    return new PEG_Context($s);
}

function token($s)
{
    return PEG_Token::get($s);
}

function __autoload($klass)
{
    include_once str_replace('_', '/', $klass) . '.php';
}