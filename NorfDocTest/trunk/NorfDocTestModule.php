<?php

class NorfDocTestModule
{
    
    private $_moduleGroup;
    private $_path;
    private $_blocks;
    private $_classDescs;
    private $_funcDescs;

    static function moduleWithContentsOfFile($path)
    {
        $module = new self($path);
        try {
            $parser = NorfDocTestParser::parseFile($path, $module);
            foreach ($parser->classDescriptions() as $class)
                $module->addClassDescription($class);
            foreach ($parser->functionDescriptions() as $func)
                $module->addFunctionDescription($func);
            return $module;
        } catch (NorfDocTestParseError $e) {
            if (NorfDocTest::isCommandLine())
                NorfDocTest::_handleDocTestParseError($e);
            else
                throw $e;
        }
    }

    function __construct($path)
    {
        $this->_path = realpath($path);
        $this->_blocks = new NorfArray();
        $this->_classDescs = new NorfArray();
        $this->_funcDescs = new NorfArray();
    }

    function moduleGroup()
    {
        return $this->_moduleGroup;
    }

    function setModuleGroup($group)
    {
        $this->_moduleGroup = $group;
    }

    function path()
    {
        return $this->_path;
    }

    function relativePath()
    {
        return NorfPathUtilities::relativePathWithPath($this->_path);
    }

    function blocks()
    {
        return $this->_blocks;
    }

    function addBlock($block)
    {
        $this->_blocks->addObjectIfAbsent($block);
    }

    function classDescriptions()
    {
        return $this->_classDescs;
    }

    function classDescriptionNamed($name)
    {
        foreach ($this->_classDescs as $class)
            if ($class->name() == $name)
                return $class;
    }

    function addClassDescription($desc)
    {
        $this->_classDescs->addObjectIfAbsent($desc);
        $desc->setModule($this);
    }

    function removeClassDescription($desc)
    {
        $this->_classDescs->removeObject($desc);
        $desc->setModule(null);
    }

    function functionDescriptions()
    {
        return $this->_funcDescs;
    }

    function addFunctionDescription($desc)
    {
        $this->_funcDescs->addObjectIfAbsent($desc);
        $desc->setModule($this);
    }

    function removeFunctionDescription($desc)
    {
        $this->_funcDescs->removeObject($desc);
        $desc->setModule(null);
    }

}

