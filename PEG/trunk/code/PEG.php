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
include_once dirname(__FILE__) . '/PEG/LookaheadNot.php';
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
include_once dirname(__FILE__) . '/PEG/Nth.php';
include_once dirname(__FILE__) . '/PEG/Flatten.php';
include_once dirname(__FILE__) . '/PEG/Drop.php';
include_once dirname(__FILE__) . '/PEG/Create.php';
                               
/**
 * PEG以下のクラスを生成するFactoryクラス。<br/>
 * このクラスのファクトリーメソッドを通じて様々なパーサを生成することができる。
 * 
 * 言葉の定義:<br/>
 * パーサ : ここではPEG_IParserインターフェイスの実装クラスを指す  <br/>
 * コンテキスト : PEG_IContextインターフェイスの実装クラスを指す  <br />
 * (パーサが)成功する : parseメソッド実行時にPEG_Failure例外が投げれずに済み、parseメソッドがが何らかの値を返す事を指す  <br/>
 * (パーサが)失敗する : parseメソッド実行時にPEG_Failureが投げられること  <br/>
 */
class PEG
{
    /**
     * PEG_Contextンスタンスを生成する。
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
     * @param PEG_IParser $p
     * @return PEG_CallbackAction
     * @see PEG_CallbackAction
     */
    static function callbackAction($callback, PEG_IParser $p)
    {
        return new PEG_CallbackAction($callback, $p);
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
     * @param PEG_IParser ...
     * @see PEG_Choice
     */
    static function choice()
    {
        return new PEG_Choice(func_get_args());
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
     * @param PEG_IParser $p
     * @return PEG_Not
     */
    static function not(PEG_IParser $p)
    {
        return new PEG_Not($p);
    }
    
    /**
     * @param PEG_IParser $p
     * @return PEG_Optional
     */
    static function optional(PEG_IParser $p)
    {
        return new PEG_Optional($p);
    }
    
    /**
     * @return PEG_Sequence
     */
    static function sequence()
    {
        return new PEG_Sequence(func_get_args());
    }
    
    /**
     * @param PEG_IParser $p
     * @return PEG_Many
     */
    static function many(PEG_IParser $p)
    {
        return new PEG_Many($p);
    }
    
    /**
     * @param PEG_IParser $p
     * @return PEG_Many1
     */
    static function many1(PEG_IParser $p)
    {
        return new PEG_Many1($p);
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
     * @param PEG_IParser $p
     * @return PEG_Lookahead
     */
    static function lookahead(PEG_IParser $p)
    {
        return new PEG_Lookahead($p);
    }
    
    /**
     * @param PEG_IParser $p
     * @return PEG_LookaheadNot
     */
    static function lookaheadNot(PEG_IParser $p)
    {
        return new PEG_LookaheadNot($p);
    }

    /**
     * @return PEG_And
     */
    static function andalso()
    {
        return new PEG_And(func_get_args());
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
        return $obj ? $obj : $obj = self::choice(self::char("\r\n"), self::token("\r\n"));
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
     * @param int $i
     * @param PEG_IParser $p
     * @return PEG_Nth
     */
    static function nth($i, PEG_IParser $p)
    {
        return new PEG_Nth($i, $p);
    }

    /**
     * @param PEG_IParser $p
     * @return PEG_Nth
     */
    static function first(PEG_IParser $p)
    {
        return self::nth(0, $p);
    }

    /**
     * @param PEG_IParser $p
     * @return PEG_Nth
     */
    static function second(PEG_IParser $p)
    {
        return self::nth(1, $p);
    }

    /**
     * @param PEG_IParser $p
     * @return PEG_Nth
     */
    static function third(PEG_IParser $p)
    {
        return self::nth(2, $p);
    }

    /**
     * @param PEG_IParser $start
     * @param PEG_IParser $body
     * @param PEG_IParser $end
     * @return PEG_Nth
     */
    static function pack(PEG_IParser $start, PEG_IParser $body, PEG_IParser $end)
    {
        return self::second(self::sequence($start, $body, $end));
    }

    /**
     * @param PEG_IParser $p
     * @return PEG_Flatten
     */
    static function flatten(PEG_IParser $p)
    {
        return new PEG_Flatten($p);
    }

    /**
     * @param PEG_IParser $p
     * @return PEG_Sequence
     */
    static function bi(PEG_IParser $p)
    {
        return self::sequence($p, $p);
    }
    
    /**
     * @param PEG_IParser $p
     * @return PEG_Sequence
     */
    static function tri(PEG_IParser $p)
    {
        return self::sequence($p, $p, $p);
    }

    /**
     * @param PEG_IParse $p
     * @return PEG_Drop 
     */
    static function drop(PEG_IParser $p)
    {
        return new PEG_Drop($p);
    }
    
    /**
     * 
     */
    static function parse(PEG_IParser $p, $str)
    {
        return $p->parse(PEG::context($str));
    }

    /**
     * @param string $klass
     * @param PEG_IParser $p
     * @return PEG_Create
     */
    static function create($klass, PEG_IParser $p)
    {
        return new PEG_Create($klass, $p);
    }
}