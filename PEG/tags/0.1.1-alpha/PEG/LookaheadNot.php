<?php

class PEG_LookaheadNot extends PEG_Lookahead
{
    function __construct(PEG_IParser $parser)
    {
        parent::__construct(new PEG_Not($parser));
    }
}