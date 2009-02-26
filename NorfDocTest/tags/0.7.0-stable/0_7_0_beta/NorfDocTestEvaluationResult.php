<?php

class NorfDocTestEvaluationResult
{

    const PASS    = 0;
    const FAILURE = 1;
    const SKIP    = 2;
    const ERROR   = 3;

    private $_block;
    private $_status;
    private $_return;
    private $_catch;
    private $_msg;
    private $_diff;
    private $_elapsedTime;

    function __construct($block, $status, $return, $catch,
                         $msg, $diff, $elapsedTime)
    {
        $this->_block = $block;
        $this->_status = $status;
        $this->_return = $return;
        $this->_catch = $catch;
        $this->_msg = $msg;
        $this->_diff = $diff;
        $this->_elapsedTime = $elapsedTime;
    }

    function block()
    {
        return $this->_block;
    }

    function status()
    {
        return $this->_status;
    }

    function isPassed()
    {
        return $this->_status == self::PASS;
    }

    function isFailed()
    {
        return $this->_status == self::FAILURE;
    }

    function isSkipped()
    {
        return $this->_status == self::SKIP;
    }

    function isError()
    {
        return $this->_status == self::ERROR;
    }

    function returnedValue()
    {
        return $this->_return;
    }

    function exception()
    {
        return $this->_catch;
    }

    function failureMessage()
    {
        return $this->_msg;
    }

    function differences()
    {
        return $this->_diff;
    }

    function elapsedTime()
    {
        return $this->_elapsedTime;
    }

}


class NorfDocTestEvaluationResultSet
{

    private $_results;
    private $_tests;
    private $_fails;
    private $_errors;
    private $_skips;
    private $_toDos;
    private $_elapsedTime;
    private $_allModules;
    private $_allClassDescs;
    private $_allMethDescDict;
    private $_allFuncDescs;

    function __construct($results, $toDos)
    {
        $this->_results = NorfArray::arrayWithArray($results);
        $this->_toDos = NorfArray::arrayWithArray($toDos);
        $this->_tests = 0;
        $this->_fails = 0;
        $this->_errors = 0;
        $this->_skips = 0;
        $this->_elapsedTime = 0;
        foreach ($results as $result)
            $this->checkResult($result);
    }

    private function checkResult($result)
    {
        $this->_tests++;
        switch ($result->status()) {
        case NorfDocTestEvaluationResult::PASS:
            break;
        case NorfDocTestEvaluationResult::FAILURE:
            $this->_fails++;
            break;
        case NorfDocTestEvaluationResult::SKIP:
            $this->_skips++;
            break;
        case NorfDocTestEvaluationResult::ERROR:
            $this->_errors++;
            break;
        }
        $this->_elapsedTime += $result->elapsedTime();
    }

    function results()
    {
        return $this->_results;
    } 

    function testCount()
    {
        return $this->_tests;
    }

    function passCount()
    {
        return $this->_tests - $this->_fails - $this->_errors - $this->_skips;
    }

    function failureCount()
    {
        return $this->_fails;
    }

    function errorCount()
    {
        return $this->_errors;
    }

    function skipCount()
    {
        return $this->_skips;
    }

    function toDoBlocks()
    {
        return $this->_toDos;
    }

    function toDoCount()
    {
        return $this->_toDos->count();
    }

    function elapsedTime()
    {
        return $this->_elapsedTime;
    }

    function allModules()
    {
        if ($this->_allModules === null)
            $this->loadFromResults();
        return $this->_allModules;
    }

    function allClassDescriptions()
    {
        if ($this->_allClassDescs === null)
            $this->loadFromResults();
        return $this->_allClassDescs;
    }

    function allMethodDescriptionForClassDescription($desc)
    {
        if ($this->_allMethDescDict === null)
            $this->loadFromResults();
        return $this->_allMethDescDict->objectForKey($desc);
    }

    function allFunctionDescriptions()
    {
        if ($this->_allFuncDescs === null)
            $this->loadFromResults();
        return $this->_allFuncDescs;
    }

    private function loadFromResults()
    {
        $this->_allModules = new NorfArray();
        $this->_allClassDescs = new NorfArray();
        $this->_allMethDescDict = new NorfDictionary();
        $this->_allFuncDescs = new NorfArray();

        foreach ($this->_results as $result) {
            $block = $result->block();
            $module = $block->module();
            $this->_allModules->addObjectIfAbsent($module);

            $blockStore = $block->blockStore();
            if (NorfClassUtilities::
                isKindOfClass($blockStore,
                              'NorfDocTestMethodDescription')) {
                $classDesc = $blockStore->classDescription();
                $this->_allClassDescs->addObjectIfAbsent($classDesc);
                $methDescs = $this->_allMethDescDict->objectForKey($classDesc);
                if ($methDescs === null) {
                    $methDescs = new NorfArray($blockStore);
                    $this->_allMethDescDict->
                        setObjectForKey($methDescs, $classDesc);
                } else
                    $methDescs->addObjectIfAbsent($classDesc);
            } else
                $this->_allFuncDescs->addObjectIfAbsent($blockStore);
        }
    }

}

