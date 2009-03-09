<?php

if (NorfPathUtilities::scriptExistsAtPath('PEAR.php')) {
    require_once 'PEAR.php';
}
if (NorfPathUtilities::scriptExistsAtPath('Text/Diff.php')) {
    require_once 'Text/Diff.php';
}

class NorfDocTestBlock
{

    private $_title;
    private $_source;
    private $_expected;
    private $_throw;
    private $_msg;
    private $_comment;
    private $_blockStore;

    function __construct($source, $expected,
                         $throw=null, $msg='', $title='', $comment='')
    {
        $this->_title = $title;
        $this->_source = $source;
        $this->_expected = $expected;
        $this->_throw = $throw;
        $this->_msg = $msg;
        $this->_comment = $comment;
        $this->_blockStore = null;
    }

    function __toString()
    {
        $s = '<' . get_class($this) . ': name=' . $this->_name .
            ', target=' . $this->fullName();

        if ($this->_throw)
            $s .= ', throw=' . $this->_throw;

        $s .= '>';
        return $s;
    }

    function module()
    {
        return $this->_blockStore->module();
    }

    function blockStore()
    {
        return $this->_blockStore;
    }

    function setBlockStore($store)
    {
        $this->_blockStore = $store;
    }

    function title()
    {
        return $this->_title;
    }

    function sourceCode()
    {
        return $this->_source;
    }

    function expectedValue()
    {
        return $this->_expected;
    }

    function comment()
    {
        return $this->_comment;
    }

    function classDescription()
    {
        if (NorfClassUtilities::isKindOfClass($this->_blockStore,
                                              'NorfDocTestClassDescription'))
            return $this->_blockStore;
        else if (NorfClassUtilities::isKindOfClass($this->_blockStore,
                                                   'NorfDocTestMethodDescription'))
            return $this->_blockStore->classDescription();
        else
            return null;
    }

    function methodDescription()
    {
        if (NorfClassUtilities::isKindOfClass($this->_blockStore,
                                              'NorfDocTestMethodDescription'))
            return $this->_blockStore;
        else
            return null;
    }

    function functionDescription()
    {
        if (NorfClassUtilities::isKindOfClass($this->_blockStore,
                                              'NorfDocTestFunctionDescription'))
            return $this->_blockStore;
        else
            return null;
    }

    function expectedException()
    {
        return $this->_throw;
    }

    function expectedExceptionMessage()
    {
        return $this->_msg;
    }

    function evaluateInContext($context)
    {
        $status = NorfDocTestEvaluationResult::PASS;
        $msg = null;
        $return = null;
        $catch = null;
        $diff = null;

        $source = $this->prepareSourceCode($this->_source, $context);
        if (!$this->_throw) {
            try {
                $beginTime = microtime(true);
                $return = $this->evaluateSourceCode($source);
                $endTime = microtime(true);
                if (NorfJSONSerialization::compareValues($return,
                                                         $this->_expected))
                    $status = NorfDocTestEvaluationResult::PASS;
                else {
                    $status = NorfDocTestEvaluationResult::FAILURE;
                    $msg = "<" .
                        var_export($this->_expected, true) .
                        "> expected but was <" .
                        var_export($return, true) . ">.";
                    if (class_exists('Text_Diff')) {
                        if (is_string($this->_expected) && is_string($return))
                            $diff = $this->differencesBetweenStrings
                                ($this->_expected, $return);
                    }
                }
            } catch (Exception $e) {
                $endTime = microtime(true);
                $status = NorfDocTestEvaluationResult::ERROR;
                $return = $e;
            }
        } else {
            try {
                $beginTime = microtime(true);
                $this->evaluateSourceCode($source);
                $endTime = microtime(true);
            } catch (Exception $e) {
                $endTime = microtime(true);
                $catch = $e;
            }
            if ($catch) {
                if (get_class($catch) == $this->_throw &&
                    $catch->getMessage() == $this->_msg)
                    $status = NorfDocTestEvaluationResult::PASS;
                else {
                    $status = NorfDocTestEvaluationResult::FAILURE;
                    $return = $catch;
                    $msg = "Exception <$this->_throw: $this->_msg> expected " .
                        "but was <" . get_class($catch) .
                        ": " . $catch->getMessage() . ">.";
                }
            } else {
                $status = NorfDocTestEvaluationResult::FAILURE;
                $msg = "Exception <$this->_throw> expected " .
                    "but none was raised.";
            }
        }

        $elapsedTime = $endTime - $beginTime;
        return new NorfDocTestEvaluationResult
            ($this, $status, $return, $catch, $msg, $diff, $elapsedTime);
    }

    const _RESERVED_WORDS = '/\Gfor|if|else|throw|try/';

    function prepareSourceCode($source, $context)
    {
        if ($context->isInClass()) {
            // #localSetUp, #localTearDown
            if ($context->isInMethod()) {
                $source = $this->sourceCodeByAddingAroundSourceCode
                    ($source, $context->methodDescription());
            }

            // #setUp, #tearDown
            $classDesc = $context->classDescription();
            $source = $this->sourceCodeByAddingAroundSourceCode
                ($source, $classDesc);

            $source = str_replace('#class', "'" .
                                $classDesc->name() . "'", $source);
            $source = str_replace('#new', 'new ' .
                                $classDesc->name(), $source);
        }

        $i = strrpos($source, ';');
        $i = strrpos(substr($source, 0, $i), ';');

        if ($i > 0) {
            $head = trim(substr($source, 0, $i+1));
            $tail = trim(substr($source, $i+1));
            if (preg_match(self::_RESERVED_WORDS, $tail) == 0)
                $source = $head . '$__NorfDocTestBlockCaseResult__ = ' . $tail;
        } else if (preg_match(self::_RESERVED_WORDS, $source) == 0)
            $source = '$__NorfDocTestBlockCaseResult__ = ' . $source;

        return $source;
    }

    private function sourceCodeByAddingAroundSourceCode($source, $blockStore)
    {
        if ($setUp = $blockStore->blockToSetUp())
            $source = $setUp->sourceCode() . $source;
        if ($tearDown = $blockStore->blockToTearDown())
            $source .= $tearDown->sourceCode();
        return $source;
    }

    private function evaluateSourceCode($__NorfDocTestSourceCode__)
    {
        $__NorfDocTestBlockCaseResult__ = null;
        eval($__NorfDocTestSourceCode__);
        return $__NorfDocTestBlockCaseResult__;
    }

    function differencesBetweenStrings($s1, $s2)
    {
        $w1 = self::wordsFromString($s1);
        $w2 = self::wordsFromString($s2);
        $diffResult = new Text_Diff('auto', array($w1, $w2));
        $strDiffs = array();
        foreach ($diffResult->getDiff() as $diff) {
            $strDiffs[] = array(implode($diff->orig), implode($diff->final));
        }
        return new NorfDocTestStringDifferences($s1, $s2, $strDiffs);
    }

    static function wordsFromString($s)
    {
        $sc = new NorfStringScanner($s);
        $words = array();
        while (!$sc->isAtEndOfString()) {
            if (($word = $sc->scanPattern('/\G[_\w]+/')) !== null)
                $words[] = $word;
            else
                $words[] = $sc->scanCharacter();
        }
        return $words;
    }

}

