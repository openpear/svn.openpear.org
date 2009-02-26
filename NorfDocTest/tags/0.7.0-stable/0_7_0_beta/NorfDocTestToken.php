<?php

class NorfDocTestToken extends NorfPHPToken
{

    const BEFORE_TAG        = '#before';
    const COMMENT_TAG       = '#comment';
    const LSETUP_TAG        = '#localSetUp';
    const LTEARDOWN_TAG     = '#localTearDown';
    const NAME_TAG          = '#name';
    const SETUP_TAG         = '#setUp';
    const SUPER_TAG         = '#super';
    const TEARDOWN_TAG      = '#tearDown';
    const TEST_TAG          = '#test';
    const TODO_TAG          = '#toDo';

    const CODE_TAG          = 'Code';
    const EXPECTED_TAG      = 'Expected';
    const END_TAG           = 'End';

    function __construct($path, $className, $funcName,
                         $line, $col, $tag, $value=null)
    {
        parent::__construct($path, $line, $col, $tag, $value);
        $this->_className = $className;
        $this->_funcName = $funcName;
    }

    function className()
    {
        return $this->_className;
    }

    function functionName()
    {
        return $this->_funcName;
    }

}

