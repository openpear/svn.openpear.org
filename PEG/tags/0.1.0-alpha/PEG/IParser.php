<?php

interface PEG_IParser
{
    // ���s������PEG_Failure�𓊂���
    function parse(PEG_IContext $c);
}
