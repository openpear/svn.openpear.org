<?php
/**
 * このPEGクラスは、静的メソッドから様々なパーサやコンテキスト等を生成するのに使われる。
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
include_once dirname(__FILE__) . '/PEG/Preg.php';
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
    
    static function preg($pattern)
    {
        return new PEG_Preg($pattern);
    }
    
    /**
     * PEG_IContextインスタンスを生成する。
     * 
     * @param string|Array $str
     * @return PEG_IContext
     * @see PEG_IContext, PEG_Context, PEG_ArrayContext
     */
    static function context($val)
    {
        return is_string($val) ? new PEG_StringContext($val) :  new PEG_ArrayContext($val);
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
        return self::callbackAction(array('PEG_Util', 'cons'), self::seq($p, self::many($p)));
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
        static $p = null;
        return $p ? $p : $p = self::choice(self::newLine(), self::eos());
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
        $curry = PEG_Curry::make(array('PEG_Util', 'at'), $key);
        return self::callbackAction($curry, $p);
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
     */
    static function flatten($p)
    {
        return self::callbackAction(array('PEG_Util', 'flatten'), $p);
    }


    /**
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
     * @param $p
     * @param string $glue
     */
    static function join($p, $glue = '')
    {
        $curry = PEG_Curry::make(array('PEG_Util', 'join'), $glue);
        return self::callbackAction($curry, $p);
    }

    static function count($p)
    {
        return self::callbackAction(array('PEG_Util', 'count'), $p);
    }
    
    static function listof($item, $glue)
    {
        $parser = PEG::seq($item, PEG::many(PEG::secondSeq($glue, $item)));
        return self::callbackAction(array('PEG_Util', 'cons'), $parser);
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
    
    static function memo($p)
    {
        return new PEG_Memoize(self::parser($p));
    }

    static function leaf()
    {
        $args = func_get_args();
        return count($args) === 1 ? self::first(new PEG_Leaf($args)) : new PEG_Leaf($args);
    }
    
    /**
     * @param PEG_IParser
     * @param string
     * @return array
     */
    static function matchAll(PEG_IParser $parser, $str)
    {
        $context = self::context($str);
        $matches = array();
        while (!$context->eos()) {
            $result = $parser->parse($context);
            if (!$result instanceof PEG_Failure) {
                $matches[] = $result;
            }
            else {
                $context->read(1);
            }
        }
        
        return $matches;
    }
}