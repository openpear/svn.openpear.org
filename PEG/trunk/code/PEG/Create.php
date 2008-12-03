<?php

/**
 * パーサの結果を与えられたクラスのコンストラクタに渡してそれを返すパーサ
 *
 */
class PEG_Create extends Action
{
    protected $klass;

    /**
     * @param string $klass クラス名
     * @param PEG_IParser $parser
     */
    function __construct($klass, PEG_IParser $parser)
    {
        $this->klass = $klass;
        parent::__construct($parser);
    }

    /**
     * @param mixed $result  
     * @return mixed
     */
    function process($result)
    {
        return new $this->klass($result);
    }
}
