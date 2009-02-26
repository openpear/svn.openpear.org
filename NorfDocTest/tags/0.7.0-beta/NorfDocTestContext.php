<?php

class NorfDocTestContext
{

    private $_moduleGroup;
    private $_request;
    private $_loggers;
    private $_totalResult;
    private $_isFinished = false;
    private $_classDesc;
    private $_methDesc;
    private $_funcDesc;
    private $_results;
    private $_resultSet;

    function __construct($request, $moduleGroup=null)
    {
        $this->_request = $request;
        if ($moduleGroup)
            $this->_moduleGroup = $moduleGroup;
        else
            $this->_moduleGroup = NorfDocTestModuleGroup::defaultGroup();
        $this->_loggers = new NorfArray(new NorfDocTestDefaultLogger());
        $this->_results = new NorfArray();
    }

    function request()
    {
        return $this->_request;
    }

    function moduleGroup()
    {
        return $this->_moduleGroup;
    }

    function resultSet()
    {
        return $this->_resultSet;
    }

    function isFinished()
    {
        return $this->_isFinished;
    }

    function isInClass()
    {
        return $this->_classDesc !== null;
    }

    function isInMethod()
    {
        return $this->_methDesc !== null;
    }

    function isInFunction()
    {
        return $this->_funcDesc !== null;
    }

    function classDescription()
    {
        return $this->_classDesc;
    }

    function methodDescription()
    {
        return $this->_methDesc;
    }

    function functionDescription()
    {
        return $this->_funcDesc;
    }

    function execute()
    {
        if ($this->_isFinished)
            throw new Exception("This context is already finished");

        $classElement = $this->_request->classSearchElement();
        $methElementDict = $this->_request->methodSearchElementDictionary();
        $funcElement = $this->_request->functionSearchElement();
        $search = $this->_moduleGroup->blocksMatchingSearchElements
            ($classElement, $methElementDict, $funcElement);

        $this->_loggers = $this->_request->loggers();
        $this->loggersWillEvaluate();
        $this->executeBlocks($search->blocksTotest());
        $this->_toDos = $search->toDoBlocks();

        $this->_resultSet = new NorfDocTestEvaluationResultSet
            ($this->_results, $this->_toDos);
        $this->loggersDidEvaluate($this->_resultSet);
    }

    protected function executeBlocks($blocks)
    {
        foreach ($blocks as $block)
            $this->executeBlock($block);
    }

    protected function executeBlock($block)
    {
        $this->_classDesc = $block->classDescription();
        $this->_methDesc = $block->methodDescription();
        $this->_funcDesc = $block->functionDescription();

        $this->loggersBlockWillEvaluate($block);
        $result = $block->evaluateInContext($this);
        $this->_results->addObject($result);
        $this->loggersBlockDidEvaluate($block, $result);
    }

    protected function loggersWillEvaluate()
    {
        foreach ($this->_loggers as $logger)
            $logger->willEvaluate($this);
    }

    protected function loggersDidEvaluate($resultSet)
    {
        foreach ($this->_loggers as $logger)
            $logger->didEvaluate($this, $resultSet);
    }

    protected function loggersBlockWillEvaluate($block)
    {
        foreach ($this->_loggers as $logger)
            $logger->blockWillEvaluate($this, $block);
    }

    protected function loggersBlockDidEvaluate($block, $result)
    {
        foreach ($this->_loggers as $logger)
            $logger->blockDidEvaluate($this, $block, $result);
    }

}

