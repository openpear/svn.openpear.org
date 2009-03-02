<?php
include_once dirname(__FILE__) . '/lime.php';
include_once dirname(__FILE__) . '/default.php';

foreach (array('/../../code') as $path) {
     set_include_path(dirname(__FILE__) . $path . PATH_SEPARATOR . get_include_path());
}

include_once 'HatenaSyntax.php';

function token($token)
{
    return PEG::token($token);
}

function context($s)
{
    return PEG::context($s);
}