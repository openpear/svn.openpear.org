<?php

// - TableLine := TableCell+ "|" EndOfLine
// - Table := TableLine+
class HatenaSyntax_Table extends PEG_Action
{
    function __construct()
    {
        $line = new PEG_Sequence();
        $line->with(new PEG_Many1(new HatenaSyntax_TableCell()))
             ->with(PEG_Token::get('|'))
             ->with(HatenaSyntax_EndOfLine::getInstance());
        $parser = new PEG_Many1($line);
        
        parent::__construct($parser);
    }
    function process($result)
    {
        $ret = array();
        foreach ($result as $elt) {
            $ret[] = $elt[0];
        }
        
        return array('type' => 'table',
                     'body' => $ret);
    }
    static function getInstance()
    {
        static $o = null;
        return $o ? $o : $o = new self;
    }
}