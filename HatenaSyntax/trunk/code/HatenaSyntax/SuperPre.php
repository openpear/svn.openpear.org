<?php

// SuperPre := ">|" (alpha alnum*)? "|" SuperPreElement+ "||<" EndOfLine @doing 
class HatenaSyntax_SuperPre implements PEG_IParser
{
    protected $p;
    function __construct()
    {
        $this->p = new PEG_Many1(new HatenaSyntax_SuperPreElement());
    }
    function parse(PEG_IContext $context)
    {
        PEG_Token::get('>|')->parse($context);
        $ext = '';
        for (;;) {
            if ($context->eos()) throw new PEG_Failure;
            $c = $context->read(1);
            if (ctype_alnum($c)) {
                $ext .= $c;
            }
            else {
                $context->seek($context->tell() - 1);
                break;
            }
        }
        PEG_Token::get('|')->parse($context);
        
        $result = $this->p->parse($context);
        
        $nl = new PEG_Optional(HatenaSyntax_NewLine::getInstance());
        $nl->parse($context);
        
        PEG_Token::get('||<')->parse($context);
        HatenaSyntax_EndOfLine::getInstance()->parse($context);
        
        return array('type' => 'superpre',
                     'ext' => $ext,
                     'body' => $result);
    }
    static function getInstance()
    {
        static $o = null;
        return $o ? $o : $o = new self;
    }
}
