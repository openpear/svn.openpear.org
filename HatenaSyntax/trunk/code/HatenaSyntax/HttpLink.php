<?php

// - HttpLink := "[" ("http://" | "https://") ((?!"]" | ":title=") LineChar)+ (":title=" ((?!"]") LineChar)+)? "]"
class HatenaSyntax_HttpLink extends PEG_Action
{
    function __construct()
    {
        $line_char = HatenaSyntax_LineChar::getInstance();
        
        $url_elt = new PEG_Sequence();
        $url_elt->with(new PEG_LookaheadNot(new PEG_Choice(array(PEG_Token::get(']'), 
                                                                 PEG_Token::get(':title=')))))
                ->with($line_char);

        $title_elt = new PEG_Sequence();
        $title_elt->with(new PEG_LookaheadNot(PEG_Token::get(']')))
                  ->with($line_char);
        
        $title = new PEG_Sequence();
        $title->with(PEG_Token::get(':title='))
              ->with(new PEG_Many1($title_elt));
        
        $parser = new PEG_Sequence();
        $parser->with(PEG_Token::get('['))
               ->with(new PEG_Choice(array(PEG_Token::get('http://'), PEG_Token::get('https://'))))
               ->with(new PEG_Many1($url_elt))
               ->with(new PEG_Optional($title))
               ->with(PEG_Token::get(']'));
        parent::__construct($parser);
    }
    function process($result)
    {
        $ret = $result[1];
        
        foreach ($result[2] as $elt) {
            $ret .= $elt[1];
        }
        
        if ($result[3]) {
            $title = '';
            foreach ($result[3][1] as $c) {
                $title .= $c[1];
            }
        }
        else {
            $title = null;
        }
        
        return array('type' => 'link', 'title' => $title, 'body' => trim($ret));
    }
    static function getInstance()
    {
        static $o = null;
        return $o ? $o : $o = new self;
    }
}