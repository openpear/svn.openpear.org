<?php

class HatenaSyntax_LineElement extends PEG_Action
{
    function __construct(PEG_IParser $arg_parser = null)
    {
        $item = new PEG_Choice(array(HatenaSyntax_Link::getInstance(), 
                                     HatenaSyntax_Footnote::getInstance(), 
                                     HatenaSyntax_LineChar::getInstance()));
        $parser = new PEG_Sequence(array(is_null($arg_parser) ?
                                         new PEG_Lookahead(PEG_Anything::getInstance()) :
                                         new PEG_LookaheadNot($arg_parser), 
                                         $item));
        parent::__construct($parser);
    }
    function process($result)
    {
        return $result[1];
    }
}