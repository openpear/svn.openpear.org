<?php
/**
 * このクラスは、静的メソッドから様々なパーサやコンテキスト等を生成するのに使われる。
 * 
 * @package PEG
 * @author anatoo<anatoo@nequal.jp>
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @version $Id$
 */

include_once dirname(__FILE__) . '/PEG/IContext.php';
include_once dirname(__FILE__) . '/PEG/IParser.php';

include_once dirname(__FILE__) . '/PEG/Action.php';
include_once dirname(__FILE__) . '/PEG/And.php';
include_once dirname(__FILE__) . '/PEG/Anything.php';
include_once dirname(__FILE__) . '/PEG/ArrayContext.php';
include_once dirname(__FILE__) . '/PEG/CallbackAction.php';
include_once dirname(__FILE__) . '/PEG/Cache.php';
include_once dirname(__FILE__) . '/PEG/Char.php';
include_once dirname(__FILE__) . '/PEG/Choice.php';
include_once dirname(__FILE__) . '/PEG/Curry.php';
include_once dirname(__FILE__) . '/PEG/EOS.php';
include_once dirname(__FILE__) . '/PEG/Failure.php';
include_once dirname(__FILE__) . '/PEG/Lookahead.php';
include_once dirname(__FILE__) . '/PEG/Many.php';
include_once dirname(__FILE__) . '/PEG/Memoize.php';
include_once dirname(__FILE__) . '/PEG/Not.php';
include_once dirname(__FILE__) . '/PEG/Optional.php';
include_once dirname(__FILE__) . '/PEG/Ref.php';
include_once dirname(__FILE__) . '/PEG/Sequence.php';
include_once dirname(__FILE__) . '/PEG/StringContext.php';
include_once dirname(__FILE__) . '/PEG/Token.php';
include_once dirname(__FILE__) . '/PEG/Util.php';

class PEG
{
    protected static function parser($val)
    {
        return is_string($val) ?  self::token($val) : $val;
    }
    
    protected static function parserArray(Array $arr)
    {
        foreach ($arr as &$val) $val = self::parser($val);
        return $arr;
    }
    
    /**
     * 引数に応じて適切なPEG_IContextインスタンスを生成する。
     * 
     * @param string|Array $str
     * @return PEG_IContext
     * @see PEG_IContext, PEG_StringContext, PEG_ArrayContext
     */
    static function context($val)
    {
        if (is_string($val)) return new PEG_StringContext($val);
        if (is_array($val)) return new PEG_ArrayContext($val);
        throw new InvalidArgumentException();
    }
    
    /**
     * PEG_CallbackActionインスタンスを生成する。
     * 
     * @param callback $callback
     * @param $p
     * @return PEG_CallbackAction
     * @see PEG_CallbackAction
     */
    static function callbackAction($callback, $p)
    {
        return new PEG_CallbackAction($callback, self::parser($p));
    }
    
    /**
     * PEG_Anythingインスタンスを得る。
     * このパーサはどのような文字でもパースに成功する
     * 
     * @return PEG_Anything
     * @see PEG_Anything
     */
    static function anything()
    {
        static $obj = null;
        return $obj ? $obj : $obj = new PEG_Anything;
    }
    
    /**
     * PEG_Choiceインスタンスを生成する。
     * このパーサは、パース時に与えられたパーサを順に試していき、初めに成功したパーサの結果をそのまま返す
     * 全てのパーサが失敗したならば、このパーサは失敗する。
     * 
     * @return PEG_Choice
     * @param ...
     * @see PEG_Choice
     */
    static function choice()
    {
        return new PEG_Choice(self::parserArray(func_get_args()));
    }
    
    /**
     * PEG_EOSインスタンスを得る。
     * このパーサは、パース時に対象が終端に来た、
     * つまり$aContext->eos() === trueの時の
     * PEG_IContextインスタンスを与えられたときのみ成功する。
     * 
     * @return PEG_EOS
     */
    static function eos()
    {
        static $obj = null;
        return $obj ? $obj : $obj = new PEG_EOS;
    }
    
    /**
     * PEG_Notインスタンスを得る。
     * このパーサは、$pパーサが成功したならば失敗し、$pパーサが失敗したならば成功する。
     * 
     * @param $p
     * @return PEG_Not
     */
    static function not($p)
    {
        return new PEG_Not(self::parser($p));
    }
    
    /**
     * 与えられたパーサが失敗した場合でもfalseを返すパーサを返す
     * 正規表現でいう"?"
     * 
     * @param $p
     * @return PEG_Optional
     */
    static function optional($p)
    {
        return new PEG_Optional(self::parser($p));
    }
    
    
    /**
     * 複数のパーサを一つにまとめる
     * 
     * @return PEG_Sequence
     */
    static function seq()
    {
        return new PEG_Sequence(self::parserArray(func_get_args()));
    }
    
    /**
     * 与えられたパーサを失敗するまで繰り返し、配列を返すパーサを得る
     * 
     * @param $p
     * @return PEG_Many
     */
    static function many($p)
    {
        return new PEG_Many(self::parser($p));
    }
    
    /**
     * 与えられたパーサを失敗するまで繰り返し、配列を返すパーサを得る
     * パーサが一度も成功しない場合は失敗する
     * 
     * @param $p
     * @return PEG_Many1
     */
    static function many1($p)
    {
        return self::callbackAction(array('PEG_Util', 'cons'), self::seq($p, self::many($p)));
    }
    
    /**
     * 与えられた文字列をそのまま返すパーサを得る
     * 
     * @param string $s
     * @return PEG_Token
     */
    static function token($s)
    {
        return PEG_Token::get($s);
    }
    
    /**
     * 与えられたパーサを実行した後、PEG_IContextの読み込み位置を元に戻すパーサを得る
     * 
     * @param $p
     * @return PEG_Lookahead
     */
    static function lookahead($p)
    {
        return new PEG_Lookahead(self::parser($p));
    }
    
    /**
     * PEG::lookahead(PEG::not($parser))と同等
     * 
     * @param $p
     * @return PEG_LookaheadNot
     */
    static function lookaheadNot($p)
    {
        return new PEG_Lookahead(PEG::not(self::parser($p)));
    }

    /**
     * 
     * @return PEG_And
     */
    static function andalso()
    {
        return new PEG_And(self::parserArray(func_get_args()));
    }

    /**
     * 与えたリファレンスをパース時にパーサとして実行するパーサを得る
     * 
     * @return PEG_Ref
     */
    static function ref(&$parser)
    {
        return new PEG_Ref($parser);
    }

    /**
     * 与えた文字列に含まれる文字にヒットするパーサを得る
     * 
     * @param string $str
     * @return PEG_Char
     */
    static function char($str)
    {
        return new PEG_Char($str);
    }

    /**
     * 数字にヒットするパーサを得る
     * 
     * @return PEG_Char
     */
    static function digit()
    {
        static $obj = null;
        return $obj ? $obj : $obj = self::char('0123456789');
    }
    
    /**
     * 改行にヒットするパーサを得る
     * 
     * @return PEG_Choice
     */
    static function newLine()
    {
        static $obj = null;
        return $obj ? $obj : $obj = self::choice(self::token("\r\n"), self::char("\r\n"));
    }
    
    /**
     * 行の終わりにヒットするパーサを返す
     * 
     * @return PEG_Choice
     */
    static function lineEnd()
    {
        static $p = null;
        return $p ? $p : $p = self::choice(self::newLine(), self::eos());
    }
    
    /**
     * アルファベットの大文字にヒットするパーサを得る
     * 
     * @return PEG_Char
     */
    static function upper()
    {
        static $obj = null;
        return $obj ? $obj : $obj = self::char('ABCDEFGHIJKLMNOPQRSTUVWXYZ');
    }
    
    /**
     * アルファベットの小文字にヒットするパーサを得る
     * 
     * @return PEG_Char
     */
    static function lower()
    {
        static $obj = null;
        return $obj ? $obj : $obj = self::char('abcdefghijklmnopqrstuvwxyz');
    }
    
    /**
     * アルファベットにヒットするパーサを得る
     * 
     * @return PEG_Choice
     */
    static function alphabet()
    {
        static $obj = null;
        return $obj ? $obj : $obj = self::choice(self::lower(), self::upper());
    }

    /**
     * 
     * 
     * @param $key
     * @param $p
     * @return PEG_At
     */
    static function at($key, $p)
    {
        $curry = PEG_Curry::make(array('PEG_Util', 'at'), $key);
        return self::callbackAction($curry, $p);
    }

    /**
     * 与えられたパーサが何か値を返したとき、その値の最初の要素を返すパーサを得る
     * 
     * @param $p
     * @return PEG_At
     */
    static function first($p)
    {
        return self::at(0, self::parser($p));
    }

    /**
     * 与えられたパーサが何か値を返したとき、その値の二番目の要素を返すパーサを得る
     * 
     * @param $p
     * @return PEG_At
     */
    static function second($p)
    {
        return self::at(1, self::parser($p));
    }

    /**
     * 与えられたパーサが何か値を返したとき、その値の三番目の要素を返すパーサを得る
     * 
     * @param $p
     * @return PEG_At
     */
    static function third($p)
    {
        return self::at(2, self::parser($p));
    }

    /**
     * $start, $body, $endの三つのパーサを一つにまとめて、$bodyの返す値のみを返すパーサを得る
     * 
     * @param $start
     * @param $body
     * @param $end
     * @return PEG_At
     */
    static function pack($start, $body, $end)
    {
        return self::second(self::seq(self::parser($start), self::parser($body), self::parser($end)));
    }

    /**
     * 与えられたパーサが返す配列を平らにするパーサを得る
     * 
     * @param $p
     */
    static function flatten($p)
    {
        return self::callbackAction(array('PEG_Util', 'flatten'), $p);
    }


    /**
     * 与えられたパーサがパース時に何を返そうともnullを返すパーサを得る
     * PEG::seqの引数に使うと、自動的に抜かされる
     * 
     * @param $p
     * @return PEG_Drop 
     */
    static function drop($p)
    {
        if (func_num_args() > 1) {
            $args = func_get_args();
            return self::callbackAction(array('PEG_Util', 'drop'), new PEG_Sequence(self::parserArray($args)));
        }
        return self::callbackAction(array('PEG_Util', 'drop'), $p);
    }

    /**
     * @param string $klass
     * @param $p
     */
    static function create($klass, $p)
    {
        $curry = PEG_Curry::make(array('PEG_Util', 'create'), $klass);
        return self::callbackAction($curry, $p);
    }
    
    /**
     * 与えれたパーサがパース時に配列を返すとして、その配列をjoinして返すパーサを得る
     * 
     * @param $p
     * @param string $glue
     */
    static function join($p, $glue = '')
    {
        $curry = PEG_Curry::make(array('PEG_Util', 'join'), $glue);
        return self::callbackAction($curry, $p);
    }

    /**
     * 与えられたパーサがパース時に何か返す時、その値をcount()した値を返すパーサを得る
     * 
     * @param $p
     * @return PEG_CallbackAction
     */
    static function count($p)
    {
        return self::callbackAction(array('PEG_Util', 'count'), $p);
    }
    
    /**
     * 
     *
     * @param $item
     * @param $glue
     * @return PEG_CallbackAction
     */
    static function listof($item, $glue)
    {
        $parser = PEG::seq($item, PEG::many(PEG::secondSeq($glue, $item)));
        return self::callbackAction(array('PEG_Util', 'cons'), $parser);
    }

    /**
     * 半角空白かタブにヒットするパーサを得る
     *
     * @return PEG_Char
     */
    static function blank()
    {
        static $obj = null;
        return $obj ? $obj : $obj = PEG::char(" \t");
    }
    
    /**
     * PEG::first(PEG::seq($a, $b, ...)) と同等
     *
     * @return PEG_At
     */
    static function firstSeq()
    {
        return self::first(new PEG_Sequence(self::parserArray(func_get_args())));
    }
    
    /**
     * PEG::second(PEG::seq($a, $b, ...)) と同等
     *
     * @return PEG_At
     */
    static function secondSeq()
    {
        return self::second(new PEG_Sequence(self::parserArray(func_get_args())));
    }
    
    /**
     * PEG::third(PEG::seq($a, $b, ...)) と同等
     *
     * @return PEG_At
     */
    static function thirdSeq()
    {
        return self::third(new PEG_Sequence(self::parserArray(func_get_args())));
    }
    
    static function subtract($p)
    {
        $args = func_get_args();
        array_shift($args);
        foreach ($args as &$elt) {
            $elt = self::not(self::parser($elt));
        }
        $args[] = self::parser($p);
        return new PEG_And($args);
    }
    
    /**
     * PEG_Failureインスタンスを返す
     *
     * @return PEG_Failure
     */
    static function failure()
    {
        return PEG_Failure::it();
    }
    
    /**
     * パーサをメモ化する
     *
     * @param $p
     * @return PEG_Memoize
     */
    static function memo($p)
    {
        return new PEG_Memoize(self::parser($p));
    }

    /**
     * パーサが最初にヒットした時に返した値を返す
     *
     * @param PEG_IParser $parser
     * @param $subject
     * @return unknown
     */
    static function match(PEG_IParser $parser, $subject)
    {
        return self::_match($parser, self::context($subject));
    }
    
    static function _match(PEG_IParser $parser, PEG_IContext $context, $need_matching_start = false)
    {
        while(!$context->eos()) {
            $start = $context->tell();
            $result = $parser->parse($context);
            $end = $context->tell();
            if ($result instanceof PEG_Failure) {
                $context->seek($start + 1);
            }
            else {
                return $need_matching_start ? array($result, $start) : $result;
            }
        }
        return $need_matching_start ? array(self::failure(), null) : self::failure();
    }
    
    /**
     * パーサがヒットした時の値を全て返す
     * 
     * @param PEG_IParser
     * @param string
     * @return array
     */
    static function matchAll(PEG_IParser $parser, $subject)
    {
        $context = self::context($subject);
        $matches = array();
        while (!$context->eos()) {
            $result = self::_match($parser, $context);
            if (!$result instanceof PEG_Failure) {
                $matches[] = $result;
            }
        }
        
        return $matches;
    }
}