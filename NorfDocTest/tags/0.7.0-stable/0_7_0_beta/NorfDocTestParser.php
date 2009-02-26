<?php

class NorfDocTestParser
{

    private $_path;
    private $_sc;
    private $_docTestSc;
    private $_classDesc;
    private $_classDescs;
    private $_funcDescs;
    private $_blockStore;
    private $_nextDocTestTokens;

    static function parseFile($path)
    {
        $parser = new self($path);
        $parser->parse();
        return $parser;
    }

    function __construct($path)
    {
        $this->_path = $path;
        $this->_nextDocTestTokens = new NorfArray();
        $this->_classDescs = new NorfArray();
        $this->_funcDescs = new NorfArray();
        $this->_blockStore = null;

        $f = fopen($path, 'r');
        $src = fread($f, filesize($path));
        fclose($f);
        $this->_sc = new NorfPHPScanner($src, $path);
    }

    function classDescriptions()
    {
        return $this->_classDescs;
    }

    function functionDescriptions()
    {
        return $this->_funcDescs;
    }

    function parse()
    {
        require_once $this->_path;

        $sc = $this->_sc;
        $braces = 0;
        $expectsClass = false;
        $expectsFunc = false;
        $inClass = false;
        $inFunc = false;
        $testTok = null;

        while ($tok = $sc->nextToken()) {
            switch ($tok->tag()) {
            case NorfPHPToken::LBRACE_TAG:
                $braces++;
                break;
            case NorfPHPToken::RBRACE_TAG:
                $braces--;
                if ($braces == 0) {
                    $inClass = false;
                    $inFunc = false;
                }
                break;
            case NorfPHPToken::DOCTEST_TAG:
                $testTok = $tok;
                break;
            case NorfPHPToken::CLASS_TAG:
                $inClass = true;
                $expectsClass = true;
                break;
            case NorfPHPToken::FUNCTION_TAG:
                $inFunc = true;
                $expectsFunc = true;
                break;
            case NorfPHPToken::IDENT_TAG:
                $parses = false;
                if ($inClass) {
                    if ($inFunc && $expectsFunc) {
                        $this->_blockStore = 
                            new NorfDocTestMethodDescription($tok->value(),
                                                             $this->_path);
                        $this->_classDesc->addMethodDescription
                            ($this->_blockStore);
                        $expectsFunc = false;
                        $parses = true;

                    } else if ($expectsClass) {
                        $this->_blockStore =
                            new NorfDocTestClassDescription($tok->value(),
                                                            $this->_path);
                        $this->_classDesc = $this->_blockStore;
                        $this->_classDescs->addObject($this->_blockStore);
                        $expectsClass = false;
                        $parses = true;
                    }
                } else if ($inFunc && $expectsFunc) {
                    $this->_blockStore =
                        new NorfDocTestFunctionDescription($tok->value(),
                                                           $this->_path);
                    $this->_funcDescs->addObject($this->_blockStore);
                    $expectsFunc = false;
                    $parses = true;
                }

                if ($parses && $testTok) {
                    $this->_docTestSc = $testTok->value();
                    $this->parseDocTest();
                    $testTok = null;
                }
                $inFunc = false;
                break;
            }
        }
    }

    function nextDocTestToken()
    {
        if ($this->_nextDocTestTokens->isEmpty())
            return $this->_docTestSc->nextToken();
        else
            return $this->_nextDocTestTokens->removeLastObject();
    }

    function ensureNextDocTestToken()
    {
        if ($this->hasNextDocTestToken())
            return $this->nextdocTestToken();
        else
            throw new NorfDocTestParseError('reached end of documentation',
                                            $this->_path,
                                            $this->_docTestSc->lineNumber());
    }

    function revertDocTestToken($tok)
    {
        $this->_nextDocTestTokens->addObject($tok);
    }

    function hasNextDocTestToken()
    {
        return !$this->_nextDocTestTokens->isEmpty() ||
            $this->_docTestSc->hasNextToken();
    }

    function parseDocTest()
    {
        while ($this->hasNextDocTestToken()) {
            if ($tok = $this->nextDocTestToken()) {
                if ($tok->tag() == NorfDocTestToken::END_TAG)
                    return;
                else
                    $this->revertDocTestToken($tok);
            }

            if ($block = $this->parseBlockToSetUp()) {
                if ($this->_classDesc) {
                    if ($this->_classDesc->blockToSetUp())
                        throw new NorfDocTestParseError
                            ('duplicated #setUp at ' .
                             $this->_classDesc->signature(), $this->_path);
                    else
                        $this->_classDesc->setBlockToSetUp($block);
                } else if ($this->_blockStore->blockToSetUp())
                        throw new NorfDocTestParseError
                            ('duplicated #setUp at ' .
                             $this->_blockStore->signature(), $this->_path);
                else
                    $this->_blockStore->setBlockToSetUp($block);

            } else if ($block = $this->parseBlockToLocalSetUp()) {
                if ($this->_blockStore->blockToSetUp())
                    throw new NorfDocTestParseError
                        ('duplicated #localSetUp at ' .
                         $this->_blockStore->signature(), $this->_path);
                else
                    $this->_blockStore->setBlockToSetUp($block);

            } else if ($block = $this->parseBlockToTearDown()) {
                if ($this->_classDesc) {
                    if ($this->_classDesc->blockToTearDown())
                        throw new NorfDocTestParseError
                            ('duplicated #tearDown at ' .
                             $this->_classDesc->signature(), $this->_path);
                    else
                        $this->_classDesc->setBlockToTearDown($block);
                } else
                    $this->_blockStore->setBlockToTearDown($block);

            } else if ($block = $this->parseBlockToLocalTearDown()) {
                if ($this->_blockStore->blockToLocalTearDown())
                    throw new NorfDocTestParseError
                        ('duplicated #localTearDown at ' .
                         $this->_blockStore->signature(), $this->_path);
                else
                    $this->_blockStore->setBlockToLocalTearDown($block);

            } else if ($block = $this->parseBlockToTest())
                $this->_blockStore->addBlockToTest($block);

            else if ($block = $this->parseToDoBlock())
                $this->_blockStore->addToDoBlock($block);

            else if ($this->parseSuper())
                $this->_blockStore->setInvokesSuperImplementation(true);

            else {
                throw new NorfDocTestParseError
                    ('parse error', $this->_path,
                     $this->_docTestSc->lineNumber());
            }
        }
    }

    function parseBlockToSetUp()
    {
        return $this->parseSpecialBlock(NorfDocTestToken::SETUP_TAG);
    }

    function parseBlockToLocalSetUp()
    {
        return $this->parseSpecialBlock(NorfDocTestToken::LSETUP_TAG);
    }

    function parseBlockToTearDown()
    {
        return $this->parseSpecialBlock(NorfDocTestToken::TEARDOWN_TAG);
    }

    function parseBlockToLocalTearDown()
    {
        return $this->parseSpecialBlock(NorfDocTestToken::LTEARDOWN_TAG);
    }

    private function parseSpecialBlock($tag)
    {
        $tok = $this->nextDocTestToken();
        if ($tok) {
            if ($tok->tag() == $tag) {
                if ($body = $this->parseBlockBody())
                    return new NorfDocTestBlock($body[0], null, $body[1]);
                else
                    throw new NorfDocTestParseError('expected \'>>>\'',
                                                    $this->_path,
                                                    $tok->lineNumber(), $tok);
            } else {
                $this->revertDocTestToken($tok);
                return null;
            }
        } else
            return null;
    }

    function parseBlockBody()
    {
        $tok = $this->nextDocTestToken();
        if ($tok) {
            if ($tok->tag() == NorfDocTestToken::COMMENT_TAG) {
                $codeTok = $this->ensureNextDocTestToken();
                if ($codeTok->tag() == NorfDocTestToken::CODE_TAG)
                    return array($codeTok->value(), $tok->value());
                else if ($codeTok)
                    throw new NorfDocTestParseError('expected \'>>>\'',
                                                    $this->_path,
                                                    $codeTok->lineNumber(),
                                                    $codeTok);
            } else if ($tok->tag() == NorfDocTestToken::CODE_TAG)
                return array($tok->value(), '');
            else {
                $this->revertDocTestToken($tok);
                return null;
            }
        } else
            return null;
    }

    function parseBlockToTest()
    {
        $tok = $this->nextDocTestToken();
        if ($tok) {
            if ($tok->tag() == NorfDocTestToken::TEST_TAG) {
                if ($body = $this->parseBlockBody()) {
                    $expectedTok = $this->ensureNextDocTestToken();
                    if ($expectedTok->tag() == NorfDocTestToken::EXPECTED_TAG) {
                        $value = $expectedTok->value();

                        // decodes expected result string
                        $throw = null;
                        $msg = null;
                        if (preg_match('/\G([a-zA-Z0-0_]+):(.*)/',
                                       $value, $matches)) {
                            $throw = $matches[1];
                            $msg = trim($matches[2]);
                        } else {
                            try {
                                $value = NorfJSONSerialization::
                                    objectFromJSON($value);
                            } catch (NorfJSONParseError $e) {
                                $msg = 'JSON: ' . $e->getMessage();
                                throw new NorfDocTestParseError
                                    ($msg, $this->_path,
                                     $expectedTok->lineNumber(),
                                     $expectedTok);
                            }
                        }
                        return new NorfDocTestBlock
                            ($body[0], $value,
                             $throw, $msg, $tok->value(), $body[1]);
                    } else {
                        $msg = 'expected <expected result>';
                        throw new NorfDocTestParseError
                            ($msg, $this->_path,
                             $expectedTok->lineNumber(), $expectedTok);
                    }
                } else
                    throw new NorfDocTestParseError('expected \'>>>\'',
                                                    $this->_path,
                                                    $tok->lineNumber(), $tok);
            } else
                $this->revertDocTestToken($tok);
        } else
            return null;
    }

    function parseToDoBlock()
    {
        $tok = $this->nextDocTestToken();
        if ($tok) {
            if ($tok->tag() == NorfDocTestToken::TODO_TAG) {
                if ($block = $this->parseBlockToTest())
                    return $block;
                else
                    throw new NorfDocTestParseError
                        ('expected \'#test\'', $this->_path,
                         $tok->lineNumber(), $tok);
            } else {
                $this->revertDocTestToken($tok);
                return null;
            }
        } else
            return null;
    }

    function parseSuper()
    {
        $tok = $this->nextDocTestToken();
        if ($tok) {
            if ($tok->tag() == NorfDocTestToken::SUPER_TAG)
                return true;
            else {
                $this->revertDocTestToken($tok);
                return false;
            }
        } else
            return false;
    }

}


class NorfDocTestParseError extends Exception
{

    private $_path;
    private $_lineNum;
    private $_tok;

    function __construct($msg, $path, $lineNum=null, $tok=null)
    {
        parent::__construct($msg);
        $this->_path = $path;
        $this->_lineNum = $lineNum;
        $this->_tok = $tok;
    }

    function path()
    {
        return $this->_path;
    }

    function lineNumber()
    {
        return $this->_lineNum;
    }

    function token()
    {
        return $this->_tok;
    }

}

