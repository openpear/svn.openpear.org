<?php
include_once dirname(__FILE__) . '/t/t.php';

class MyActionParser extends PEG_Action
{
    function process($result)
    {
        return join('', $result);
    }
}

$lime = new lime_test;
$action = new MyActionParser(new PEG_Many(PEG::token('hoge')));

$lime->is('hogehogehogehoge', $action->parse(PEG::context('hogehogehogehoge')));