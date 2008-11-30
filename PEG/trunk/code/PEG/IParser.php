<?php

interface PEG_IParser
{
    /**
     * 
     * パースに失敗した場合はPEG_Failureを投げる
     *
     * @param PEG_IContext $c
     */
    function parse(PEG_IContext $c);
}
