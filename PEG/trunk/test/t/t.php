<?php
include_once dirname(__FILE__) . '/default.php';
include_once dirname(__FILE__) . '/lime.php';
include_once dirname(__FILE__) . '/../../code/PEG.php';

function context($s)
{
    return new PEG_Context($s);
}

function token($s)
{
    return PEG_Token::get($s);
}

