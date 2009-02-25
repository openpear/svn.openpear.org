<?php

/**
 * パーサインターフェイス
 *
 */
interface PEG_IParser
{
    /**
     * パースに失敗した場合はPEG_Failureを投げる。
     * 成功した場合はなんらかの値を返す。
     * 
     * @param PEG_IContext $c
     * @return mixed
     */
    function parse(PEG_IContext $c);
}
