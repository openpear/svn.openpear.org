<?php

interface PEG_IParser
{
    // 失敗したらPEG_Failureを投げる
    function parse(PEG_IContext $c);
}
