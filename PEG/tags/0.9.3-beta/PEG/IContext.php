<?php
/**
 * PEG_IParserが必要とするコンテキスト
 * 
 * @package PEG
 * @author anatoo<anatoo@nequal.jp>
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @version $Id$
 */

interface PEG_IContext
{
    /**
     * 対象の現在の位置を得る。
     * 
     * @return int
     */
    function tell();
    
    /**
     * 対象の現在の位置を設定する。
     *
     * @param int $i
     */
    function seek($i);
    
    /**
     * 対象の一部を返す。その際に現在位置も変更する。
     *
     * @param int $i
     * @return string
     */
    function read($i);
    
    /**
     * 読み込むべきものが無い場合trueを返す。
     * @return bool
     */
    function eos();
    
    /**
     * コンテキストが持つ対象全体を返す
     */
    function get();
    
    /**
     * @param PEG_IParser 
     * @param int 
     * @param ? 
     */
    function save(PEG_IParser $parser, $start, $end, $val);
    
    /**
     * array(hit, array(end, val))を返す
     * 
     * @param PEG_IParser
     * @param int
     * @return array
     */
    function cache(PEG_IParser $parser);
}