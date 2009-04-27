<?php

abstract class NorfDocTestBlockStore
{

    private $_blocksToTest;
    private $_blockToSetUp = null;
    private $_blockToTearDown = null;
    private $_toDoBlocks;
    private $_invokesSuper = false;

    abstract function signature();

    function __construct()
    {
        $this->_blocksToTest = new NorfArray();
        $this->_toDoBlocks = new NorfArray();
        $this->_invokesSuper = false;
        $this->_blockToSetUp = null;
        $this->_blockToTearDown = null;
    }

    function allBlocks()
    {
        $blocks = new NorfArray();
        if ($this->_blockToSetUp)
            $blocks->addObject($this->_blockToSetUp);
        if ($this->_blockToTearDown)
            $blocks->addObject($this->_blockToTearDown);
        $blocks->addObjectsFromArray($this->_blocksToTest);
        $blocks->addObjectsFromArray($this->_toDoBlocks);
        return $blocks;
    }

    function blocksToTest()
    {
        return $this->_blocksToTest;
    }

    function blockToTestNamed($name)
    {
        foreach ($this->_blocksToTest as $block)
            if ($block->name() == $name)
                return $block;
    }

    function hasBlocksToTest()
    {
        foreach ($this->_blocksToTest as $block)
            if ($block->canEvaluate())
                return true;
        return false;
    }

    function addBlockToTest($block)
    {
        $this->_blocksToTest->addObject($block);
        $block->setBlockStore($this);
    }

    function removeBlockToTest($block)
    {
        $this->_blocksToTest->removeObject($block);
        $block->setBlockStore($this);
    }

    function blockToSetUp()
    {
        return $this->_blockToSetUp;
    }

    function setBlockToSetUp($block)
    {
        $this->_blockToSetUp = $block;
    }

    function blockToTearDown()
    {
        return $this->_blockToTearDown;
    }

    function setBlockToTearDown($block)
    {
        $this->_blockToTearDown = $block;
    }

    function toDoBlocks()
    {
        return $this->_toDoBlocks;
    }

    function addToDoBlock($block)
    {
        $this->_toDoBlocks->addObject($block);
        $block->setBlockStore($this);
    }

    function removeToDoBlock($block)
    {
        $this->_toDoBlocks->removeObject($block);
        $block->setBlockStore($this);
    }

    function invokesSuperImplementation()
    {
        return $this->_invokesSuper;
    }

    function setInvokesSuperImplementation($flag)
    {
        $this->_invokesSuper = $flag;
    }

}

