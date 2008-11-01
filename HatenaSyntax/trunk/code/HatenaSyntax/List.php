<?php

// - ListLine := ("+" | "-")+ Line<-(optional=true) EndOfLine
// - List := ListLine+

class HatenaSyntax_List extends PEG_Action
{
    function __construct()
    {
        $line = new PEG_Sequence;
        $line->with(new PEG_Many1(new PEG_Choice(array(PEG_Token::get('-'),
                                                       PEG_Token::get('+')))))
             ->with(new HatenaSyntax_Line(true))
             ->with(HatenaSyntax_EndOfLine::getInstance());
        $line = new PEG_CallbackAction(array($this, 'processLine'), $line);
        
        parent::__construct(new PEG_Many1($line));
    }
    function process($result)
    {
        return $this->make($result);
    }
    protected function make(Array &$arr, $level = 0)
    {
        $body = array();

        if ($elt = current($arr)) {
            $head = $elt['type'];
        }
        else {
            $head = '';
        }
        
        while ($elt = current($arr)) {
            if ($elt['level'] === $level) {
                $body[] = $elt['body'];
                next($arr);
            }
            elseif ($elt['level'] > $level) {
                $body[] = $this->make($arr, $level + 1);
            }
            else {
                break;
            }   
        }
        
        return array('type' => 'list',
                     'head' => $head,
                     'body' => $body);
    }
    function processLine($result)
    {
        $type = end($result[0]); // '+' or '-'
        $level = count($result[0]) - 1;
        $body = $result[1];
        return array('type' => $type,
                     'level' => $level,
                     'body' => $body);
    }
}