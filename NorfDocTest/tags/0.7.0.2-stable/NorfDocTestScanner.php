<?php

class NorfDocTestScanner extends NorfStringScanner
{
    
    const NAME_DIRECTIVE = "#name";
    const TEST_DIRECTIVE = "#test";
    const THROW_DIRECTIVE = "#throw";
    const SETUP_DIRECTIVE = "#setUp";
    const LSETUP_DIRECTIVE = "#localSetUp";
    const TEARDOWN_DIRECTIVE = "#tearDown";
    const LTEARDOWN_DIRECTIVE = "#localTearDown";
    const SUPER_DIRECTIVE = "#super";
    const TODO_DIRECTIVE = "#toDo";
    const BEFORE_DIRECTIVE = "#before";
    const CODE_MARKER = ">>>";

    protected $_tempLines = 0;
    protected $_tempColumns = 0;

    function __construct($className, $funcName, $src, $lc, $path) {
        parent::__construct($src, $lc, " \t");
        $this->_className = $className;
        $this->_funcName = $funcName;
        $this->_path = $path;
        $this->_term = false;
        $this->_expected = null;
    }

    function className() {
        return $this->_className;
    }

    function functionName() {
        return $this->_funcName;
    }

    function hasNextToken()
    {
        return !($this->_term || $this->isAtEndOfString());
    }

    function nextToken()
    {
        if ($this->_term || $this->isAtEndOfString())
            return null;
        else if ($this->_expected) {
            $e = $this->_expected;
            $this->_expected = null;
            return $e;
        }

        $this->_tempLines = $this->lineNumber();
        $this->_tempColumns = $this->characterColumnNumber();

        while (!$this->isAtEndOfString()) {
            if ($this->scanString("*/")) {
                $this->_term = true;
                return $this->createToken(NorfDocTestToken::END_TAG);; 
            } else {
                $this->scanMargin();
                if ($this->scanString(self::NAME_DIRECTIVE)) {
                    $s = trim($this->scanUpToNewLine());
                    return $this->createToken(NorfDocTestToken::NAME_TAG, $s);
                } else if ($this->scanString(self::TEST_DIRECTIVE)) {
                    $s = trim($this->scanUpToNewLine());
                    return $this->createToken(NorfDocTestToken::TEST_TAG, $s);
                } else if ($this->scanString(self::THROW_DIRECTIVE)) {
                    $s = trim($this->scanUpToNewLine());
                    return $this->createToken(NorfDocTestToken::THROW_TAG, $s);
                } else if ($this->scanString(self::BEFORE_DIRECTIVE)) {
                    $s = trim($this->scanUpToNewLine());
                    return $this->createToken(NorfDocTestToken::BEFORE_TAG, $s);
                } else if ($this->scanString(self::SETUP_DIRECTIVE)) {
                    $this->scanUpToNewLine();
                    return $this->createToken(NorfDocTestToken::SETUP_TAG, null);
                } else if ($this->scanString(self::LSETUP_DIRECTIVE)) {
                    $this->scanUpToNewLine();
                    return $this->createToken(NorfDocTestToken::LSETUP_TAG, null);
                } else if ($this->scanString(self::TEARDOWN_DIRECTIVE)) {
                    $this->scanUpToNewLine();
                    return $this->createToken(NorfDocTestToken::TEARDOWN_TAG, null);
                } else if ($this->scanString(self::LTEARDOWN_DIRECTIVE)) {
                    $this->scanUpToNewLine();
                    return $this->createToken(NorfDocTestToken::LTEARDOWN_TAG, null);
                } else if ($this->scanString(self::SUPER_DIRECTIVE)) {
                    $this->scanUpToNewLine();
                    return $this->createToken(NorfDocTestToken::SUPER_TAG, null);
                } else if ($this->scanString(self::TODO_DIRECTIVE)) {
                    $s = $this->scanUpToNewLine();
                    return $this->createToken(NorfDocTestToken::TODO_TAG, $s);
                } else if ($s = $this->scanPattern('/\G#[a-zA-Z0-9_]+/')) {
                    $msg = "unknown directive `$s'";
                    throw new NorfDocTestParseError
                        ($msg, $this->_path, $this->lineNumber());
                } else if ($this->scanString('#')) {
                    $s = trim($this->scanUpToNewLine());
                    while (!$this->scanStringNoAdvance("*/")) {
                        $this->scanMargin();
                        if ($this->scanString('# '))
                            $s .= trim($this->scanUpToNewLine());
                        else
                            break;
                    }
                    return $this->createToken(NorfDocTestToken::COMMENT_TAG, $s);
                } else if ($this->scanStringNoAdvance(self::CODE_MARKER)) {
                    $code = '';
                    for (;;) {
                        if ($this->scanString(self::CODE_MARKER)) {
                            $s = $this->scanUpToNewLine();
                            $code .= NorfPHPScanner::
                                sourceCodeByRemovingComments($s, $this->_path);
                        } else if ($this->scanString('//'))
                            $this->scanUpToNewLine();
                        else
                            break;
                        $this->scanPattern('/\G\*+(?:[^\/])/');
                    }

                    // expected
                    $expected = '';
                    while (!$this->isNextLocationAtEndOfString() &&
                           !$this->scanStringNoAdvance("*/")) {
                        $this->scanMargin();
                        if ($this->scanString('//'))
                            $this->scanUpToNewLine();
                        else if ($this->scanPatternNoAdvance('/\G[^#\r\n\t]/')
                            !== null) {
                            $s = trim($this->scanUpToNewLine());
                            if ($s !== '')
                                $expected .= $s;
                            else
                                break;
                        } else
                            break;
                    }

                    if ($expected !== '')
                        $this->_expected =
                            $this->createToken(NorfDocTestToken::
                                               EXPECTED_TAG, $expected);
                    return $this->createToken(NorfDocTestToken::CODE_TAG, $code);
                } else
                    $this->scanUpToNewLine();
            }
        }
        $this->_term = true;
        return null;
    }

    function scanMargin() {
        return $this->scanPattern('/\G\*+/');
    }

    function createToken($tag, $val=null)
    {
        return new NorfDocTestToken($this->_path,
                                    $this->_className,
                                    $this->_funcName,
                                    $this->_tempLines,
                                    $this->_tempColumns,
                                    $tag, $val);
    }

    function __toString() {
        return "<NorfDocTestScanner: line $this->_lc at '$this->_path'>";
    }

}

