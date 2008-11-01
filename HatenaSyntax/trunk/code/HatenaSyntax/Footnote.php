<?php

class HatenaSyntax_Footnote extends PEG_Action
{
    function __construct()
    {
        $content_elt = new PEG_Sequence();
        $content_elt->with(new PEG_LookaheadNot(PEG_Token::get('))')))
                    ->with(new PEG_Choice(array(HatenaSyntax_Link::getInstance(), HatenaSyntax_LineChar::getInstance())));
        $parser = new PEG_Sequence();
        $parser->with(PEG_Token::get('(('))
               ->with(new PEG_Many1($content_elt))
               ->with(PEG_Token::get('))'));
        parent::__construct($parser);
    }
    function process($result)
    {
        $ret = '';
        foreach ($result[1] as $elt) $ret .= $elt[1];
        return array('type' => 'footnote', 'body' => $ret);
    }
    static function getInstance()
    {
        static $o = null;
        return $o ? $o : $o = new self;
    }
}