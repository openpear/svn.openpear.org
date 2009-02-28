<?php
include_once dirname(__FILE__) . '/PEG/IContext.php';
include_once dirname(__FILE__) . '/PEG/IParser.php';
include_once dirname(__FILE__) . '/PEG/Action.php';
include_once dirname(__FILE__) . '/PEG/Anything.php';
include_once dirname(__FILE__) . '/PEG/CallbackAction.php';
include_once dirname(__FILE__) . '/PEG/Choice.php';
include_once dirname(__FILE__) . '/PEG/Context.php';
include_once dirname(__FILE__) . '/PEG/EOS.php';
include_once dirname(__FILE__) . '/PEG/Failure.php';
include_once dirname(__FILE__) . '/PEG/Lookahead.php';
include_once dirname(__FILE__) . '/PEG/Many.php';
include_once dirname(__FILE__) . '/PEG/Many1.php';
include_once dirname(__FILE__) . '/PEG/Not.php';
include_once dirname(__FILE__) . '/PEG/Optional.php';
include_once dirname(__FILE__) . '/PEG/Sequence.php';
include_once dirname(__FILE__) . '/PEG/Token.php';
include_once dirname(__FILE__) . '/PEG/And.php';
include_once dirname(__FILE__) . '/PEG/LineEnd.php';
include_once dirname(__FILE__) . '/PEG/Ref.php';
include_once dirname(__FILE__) . '/PEG/Char.php';
include_once dirname(__FILE__) . '/PEG/At.php';
include_once dirname(__FILE__) . '/PEG/Flatten.php';
include_once dirname(__FILE__) . '/PEG/Drop.php';
include_once dirname(__FILE__) . '/PEG/Create.php';
include_once dirname(__FILE__) . '/PEG/Join.php';
include_once dirname(__FILE__) . '/PEG/Count.php';
                               
/**
 * PEG以下のクラスを生成するFactoryクラス。<br/>
 * このクラスのファクトリーメソッドを通じて様々なパーサを生成することができる。
 * 
 * 言葉の定義:<br/>
 * パーサ : ここではPEG_IParserインターフェイスの実装クラスを指す  <br/>
 * コンテキスト : PEG_IContextインターフェイスの実装クラスを指す  <br/>
 * (パーサが)成功する : parseメソッド実行時にPEG_Failure例外が投げれずに済み、parseメソッドがが何らかの値を返す事を指す  <br/>
 * (パーサが)失敗する : parseメソッド実行時にPEG_Failureが投げられること  <br/>
 */
class PEG
{
    const VER = 0.15;
    
    static function parser($val)
    {
        return is_string($val) ?  self::token($val) : $val;
    }
    
    static function parserArray(Array $arr)
    {
        foreach ($arr as &$val) $val = self::parser($val);
        return $arr;
    }
    
    /**
     * PEG_Contextインスタンスを生成する。
     * 
     * @param string $str
     * @return PEG_Context
     * @see PEG_Context
     */
    static function context($str, $enc = null)
    {
        return new PEG_Context($str, $enc);
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
        return PEG_Anything::getInstance();
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
     * このパーサは、パース時に文字列が終端に来た、
     * つまり$aContext->eos() === trueの時の
     * PEG_IContextインスタンスを与えられたときのみ成功する。
     * 
     * @return PEG_EOS
     */
    static function eos()
    {
        return PEG_EOS::getInstance();
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
     * @param $p
     * @return PEG_Optional
     */
    static function optional($p)
    {
        return new PEG_Optional(self::parser($p));
    }
    
    /**
     * @return PEG_Sequence
     */
    static function sequence()
    {
        return new PEG_Sequence(self::parserArray(func_get_args()));
    }
    
    /**
     * @return PEG_Sequence
     */
    static function seq()
    {
        return new PEG_Sequence(self::parserArray(func_get_args()));
    }
    
    /**
     * @param $p
     * @return PEG_Many
     */
    static function many($p)
    {
        return new PEG_Many(self::parser($p));
    }
    
    /**
     * @param $p
     * @return PEG_Many1
     */
    static function many1($p)
    {
        return new PEG_Many1(self::parser($p));
    }
    
    /**
     * @param string $s
     * @return PEG_Token
     */
    static function token($s)
    {
        return PEG_Token::get($s);
    }
    
    /**
     * @param $p
     * @return PEG_Lookahead
     */
    static function lookahead($p)
    {
        return new PEG_Lookahead(self::parser($p));
    }
    
    /**
     * @param $p
     * @return PEG_LookaheadNot
     */
    static function lookaheadNot($p)
    {
        return new PEG_Lookahead(PEG::not(self::parser($p)));
    }

    /**
     * @return PEG_And
     */
    static function andalso()
    {
        return new PEG_And(self::parserArray(func_get_args()));
    }

    /**
     * @return PEG_Ref
     */
    static function ref()
    {
        return new PEG_Ref;
    }

    /**
     * @param string $str
     * @return PEG_Char
     */
    static function char($str)
    {
        return new PEG_Char($str);
    }

    /**
     * @return PEG_Char
     */
    static function digit()
    {
        static $obj = null;
        return $obj ? $obj : $obj = self::char('0123456789');
    }
    
    /**
     * @return PEG_Choice
     */
    static function newLine()
    {
        static $obj = null;
        return $obj ? $obj : $obj = self::choice(self::token("\r\n"), self::char("\r\n"));
    }
    
    /**
     * @return PEG_Choice
     */
    static function lineEnd()
    {
        static $obj = null;
        return $obj ? $obj : $obj = self::choice(self::newLine(), self::eos());
    }
    
    /**
     * @return PEG_Char
     */
    static function upper()
    {
        static $obj = null;
        return $obj ? $obj : $obj = self::char('ABCDEFGHIJKLMNOPQRSTUVWXYZ');
    }
    
    /**
     * @return PEG_Char
     */
    static function lower()
    {
        static $obj = null;
        return $obj ? $obj : $obj = self::char('abcdefghijklmnopqrstuvwxyz');
    }
    
    /**
     * @return PEG_Choice
     */
    static function alphabet()
    {
        static $obj = null;
        return $obj ? $obj : $obj = self::choice(self::lower(), self::upper());
    }

    /**
     * @param $key
     * @param $p
     * @return PEG_At
     */
    static function at($key, $p)
    {
        return new PEG_At($key, self::parser($p));
    }

    /**
     * @param $p
     * @return PEG_At
     */
    static function first($p)
    {
        return self::at(0, self::parser($p));
    }

    /**
     * @param $p
     * @return PEG_At
     */
    static function second($p)
    {
        return self::at(1, self::parser($p));
    }

    /**
     * @param $p
     * @return PEG_At
     */
    static function third($p)
    {
        return self::at(2, self::parser($p));
    }

    /**
     * @param $start
     * @param $body
     * @param $end
     * @return PEG_At
     */
    static function pack($start, $body, $end)
    {
        return self::second(self::sequence(self::parser($start), self::parser($body), self::parser($end)));
    }

    /**
     * @param $p
     * @return PEG_Flatten
     */
    static function flatten($p)
    {
        return new PEG_Flatten(self::parser($p));
    }


    /**
     * @param $p
     * @return PEG_Drop 
     */
    static function drop($p)
    {
        if (func_num_args() > 1) {
            return new PEG_Drop(new PEG_Sequence(self::parserArray(func_get_args())));
        }
        return new PEG_Drop(self::parser($p));
    }

    /**
     * @param string $klass
     * @param $p
     * @return PEG_Create
     */
    static function create($klass, $p)
    {
        return new PEG_Create($klass, self::parser($p));
    }
    
    /**
     * 
     *
     * @param $p
     * @param string $glue
     */
    static function join($p, $glue = '')
    {
        return new PEG_Join(self::parser($p), $glue);
    }

    static function count($p)
    {
        return new PEG_Count(self::parser($p));
    }

    static function blank()
    {
        static $obj = null;
        return $obj ? $obj : $obj = PEG::char(" \t");
    }
    
    static function firstSeq()
    {
        return self::first(new PEG_Sequence(self::parserArray(func_get_args())));
    }
    
    static function secondSeq()
    {
        return self::second(new PEG_Sequence(self::parserArray(func_get_args())));
    }
    
    static function thirdSeq()
    {
        return self::third(new PEG_Sequence(self::parserArray(func_get_args())));
    }
    
    static function failure()
    {
        return PEG_Failure::it();
    }
}