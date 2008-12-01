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
                               
/**
 * PEG以下のクラスを生成するFactoryクラス
 *
 */
class PEG
{
    /**
     * @param string $str
     * @return PEG_Context
     */
    static function context($str)
    {
        return new PEG_Context($str);
    }
    
    /**
     * @param callback $callback
     * @param PEG_IParser $p
     * @return PEG_CallbackAction
     */
    static function callbackAction($callback, PEG_IParser $p)
    {
        return new PEG_CallbackAction($callback, $p);
    }
    
    /**
     * @return PEG_Anything
     */
    static function anything()
    {
        return PEG_Anything::getInstance();
    }
    
    /**
     * @return PEG_Choice
     */
    static function choice()
    {
        return new PEG_Choice(func_get_args());
    }
    
    /**
     * @return PEG_EOS
     */
    static function eos()
    {
        return PEG_EOS::getInstance();
    }
    
    /**
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
     * @return PEG_LineEnd
     */
    static function lineEnd()
    {
        return PEG_LineEnd::getInstance();
    }
}