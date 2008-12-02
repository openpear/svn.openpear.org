<?php

/**
 * PEG_IParserインターフェイスが文字列を読み込む際に必要とするインターフェイス。
 *
 * @see PEG_IParser
 */
interface PEG_IContext
{
    /**
     * 文字列のインデックスの位置を得る。
     * 
     * @return int
     */
    function tell();
    
    /**
     * 文字列のインデックスの位置を設定する。
     *
     * @param int $i
     */
    function seek($i);
    
    /**
     * 文字列を読み込む。その際にインデックスの位置も変更する。
     *
     * @param int $i
     * @return string
     */
    function read($i);
    
    /**
     * 読み込むべき文字列がもう無い場合trueを返す。
     * @return bool
     */
    function eos();
}