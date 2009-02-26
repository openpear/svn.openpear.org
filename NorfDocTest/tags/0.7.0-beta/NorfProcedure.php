<?php

class NorfProcedure
{

    static function procedureWithFormat($format)
    {
        preg_match('/^([^:]+):(.*)$/s', $format, $match);
        if ($match) {
            $args = split('[ ]*,[ ]*', $match[1]);
            foreach ($args as $i => $arg)
                $args[$i] = ltrim($arg, '$');
            $code = $match[2];
        } else {
            $args = array();
            $code = $format;
        }

        $decl = '$' . join(', $', $args);
        $name = create_function($decl, $code);
        return $this->procedureWithFunction($name);
    }

    static function procedureWithObject($obj, $meth)
    {
        return $this->procedureWithFunction(array($obj, $meth));
    }

    static function procedureToReturnArgumentItself()
    {
        return self::procedureWithFormat('$arg: return $arg');
    }

    static function procedureWithFunction($name, $args=null)
    {
        return $this->alloc()->initWithFunction($name, $args);
    }

    function initWithFunction($name, $args=null)
    {
        $this->init();
        $this->_name = $name;
        if ($args)
            $this->_args = $args;
        else
            $this->_args = new NorfArray();
        return $this;
    }

    function name()
    {
        return $this->_name;
    }

    function addArgument($arg)
    {
        $this->_args->addObject($arg);
    }

    function addArguments()
    {
        $args = func_get_args();
        $this->_args->addObjectsFromPHPArray($args);
    }

    function addArgumentsFromArray($array)
    {
        $this->_args->addObjectsFromArray($args);
    }

    function procedureByAddingArgument($arg)
    {
        return $this->procedureByAddingArguments($arg);
    }

    function procedureByAddingArguments()
    {
        $args = func_get_args();
        $proc = $this->klass()->procedureWithFunction($this->_name);
        $proc->addArgumentsFromArray($this->_args);
        $proc->addArgumentsFromPHPArray($args);
        return $proc;
    }

    function procedureByAddingArgumentsFromArray($anArray)
    {
        $proc = $this->klass()->procedureWithFunction($this->_name);
        $proc->addArgumentsFromArray($this->_args);
        $proc->addArgumentsFromArray($anArray);
        return $proc;
    }

    function apply()
    {
        return call_user_func_array($this->_name, $this->_args->objects());
    }

    function applyWithArgument($arg)
    {
        return $this->applyWithArguments($arg);
    }

    function applyWithArguments()
    {
        $args = func_get_args();
        $args = array_merge($this->_args->objects(), $args);
        return call_user_func_array($this->_name, $args);
    }

    function applyWithArgumentsInArray($array)
    {
        $args = array_merge($this->_args, $array->objects());
        return call_user_func_array($this->_name, $args);
    }

}

