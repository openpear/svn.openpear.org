<?php

// Pre := ">|" (NewLine LineSegment<-( "|<" EndOfLine, true))+ "|<" EndOfLine 
class HatenaSyntax_Pre extends PEG_Action
{
    function __construct()
    {
        $newline = HatenaSyntax_NewLine::getInstance();
        
        $eol = HatenaSyntax_EndOfLine::getInstance();
        
        $cond = new PEG_Sequence();
        $cond->with(PEG_Token::get('|<'))
             ->with($eol);
        
        $line = new PEG_Sequence();
        $line->with($newline)
             ->with(new HatenaSyntax_LineSegment($cond, true));
        
        $parser = new PEG_Sequence();
        $parser->with(PEG_Token::get('>|'))
               ->with(new PEG_Many1($line))
               ->with(PEG_Token::get('|<'))
               ->with($eol);
        parent::__construct($parser);
    }
    function process($result)
    {
        $ret = array();
        $arr = $result[1];
        foreach ($arr as $elt) if (count($elt[1])) {
            $ret[] = $elt[1];
        }
        return array('type' => 'pre',
                     'body' => $ret);
    }
    static function getInstance()
    {
        static $o = null;
        return $o ? $o : $o = new self;
    }
}