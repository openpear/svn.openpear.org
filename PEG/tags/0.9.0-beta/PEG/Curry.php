<?php

class PEG_Curry
{
    protected $args, $callback;
    
    protected function __construct($callback, Array $args)
    {
        $this->callback = $callback;
        $this->args = $args;
    }
    function process($last)
    {
        $args = $this->args;
        $args[] = $last;
        
        return call_user_func_array($this->callback, $args);
    }
    
    static function make($callback)
    {
        $args = func_get_args();
        array_shift($args);
        $curry = new self($callback, $args);
        return array($curry, 'process');
    }
}