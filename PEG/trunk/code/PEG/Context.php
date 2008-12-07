<?php

/**
 * PEG_IContextの実装クラス
 *
 * @see PEG::context()
 */
class PEG_Context implements PEG_IContext
{
    protected $s, $i = 0, $len, $enc;
    
    /**
     * 与えられた文字列とその位置を保持するPEG_Contextインスタンスを生成する。
     *
     * @param string $s 文字列
     * @param string $enc 文字コード
     */
    function __construct($s, $enc = null) { 
        $this->s = $s; 
        $this->len = strlen($s);
        if (is_null($enc)) {
            $enc = mb_detect_encoding($s);
        }
        $this->encoding = $enc;
    }

    /**
     * @param int $i
     * @return string
     */
    function read($i)
    {
        if ($this->eos() && $i > 0) throw new PEG_Failure();
        $this->i += $i;
        return substr($this->s, $this->i - $i, $i);
    }
    
    /**
     * @param int $i
     * @return bool
     */
    function seek($i)
    {
        if ($this->len < $i) return false;
        $this->i = $i;
        return true;
    }
    
    /**
     * @return int
     */
    function tell()
    {
        return $this->i;
    }

    /**
     * @return bool
     */
    function eos()
    {
        return $this->len <= $this->i;
    }

    /**
     * @return string
     */
    function encoding()
    {
        return $this->encoding;
    }
}