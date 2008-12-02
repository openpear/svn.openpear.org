<?php
/**
 * パーサ同士がお互いに依存しているときに使ったりする
 * 例えばコンストラクタでお互いのインスタンスを要求するときなど
 *
 */
class PEG_Ref implements PEG_IParser
{
    function set(PEG_IParser $p)
    {
        $this->parser = $p;
    }
    function parse(PEG_IContext $c)
    {
        return $this->parser->parse($c);
    }
}