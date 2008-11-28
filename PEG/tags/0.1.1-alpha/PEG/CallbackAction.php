<?php

class PEG_CallbackAction extends PEG_Action
{
    protected $callback;
    function __construct($callback, PEG_IParser $parser)
    {
        if (!is_callable($callback) && !function_exists($callback)) throw new InvalidArgumentException('first argument must be callable');
        $this->callback = $callback;
        parent::__construct($parser);
    }
    protected function process($result)
    {
        return call_user_func($this->callback, $result);
    }
}