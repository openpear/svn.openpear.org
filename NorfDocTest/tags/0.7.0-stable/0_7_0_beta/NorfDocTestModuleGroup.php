<?php

class NorfDocTestModuleGroup
{

    private static $_default;
    private $_modules;

    static function defaultGroup()
    {
        if (self::$_default == null)
            self::$_default = new self();
        return self::$_default;
    }

    function __construct()
    {
        $this->_modules = new NorfArray();
    }

    function modules()
    {
        return $this->_modules;
    }

    function addModuleWithFile($path)
    {
        $module = NorfDocTestModule::moduleWithContentsOfFile($path);
        $this->_modules->addObject($module);
    }

    function addModule($module)
    {
        $this->_modules->addObject($module);
    }

    function removeModule($module)
    {
        $this->_modules->removeObject($module);
    }

    function executeRequest($request)
    {
        $context = new NorfDocTestContext($request, $this);
        $context->execute();
        return $context->resultSet();
    }

    function blocksMatchingSearchElements($classElement,
                                          $methElementDict,
                                          $funcElement)
    {
        $toTest = new NorfArray();
        $toDos = new NorfArray();

        foreach ($this->_modules as $module) {
            foreach ($module->classDescriptions() as $classDesc) {
                if ($classElement->matchesObject($classDesc->name())) {
                    $toTest->addObjectsFromArray($classDesc->blocksToTest());
                    $toDos->addObjectsFromArray($classDesc->toDoBlocks());

                    $methElement = $methElementDict->
                        objectForKey($classDesc->name());
                    foreach ($classDesc->methodDescriptions() as $methDesc) {
                        if (!$methDesc->isAbstract() &&
                            (!$methElement ||
                             $methElement->matchesObject($methDesc->name()))) {
                            foreach ($methDesc->blocksToTest() as $block) {
                                $toTest->addObject($block);
                            }
                            $toDos->addObjectsFromArray($methDesc->toDoBlocks());
                        }
                    }
                }
            }

            foreach ($module->functionDescriptions() as $funcDesc) {
                if ($funcElement->matchesObject($funcDesc->name())) {
                    foreach ($funcDesc->blocksToTest() as $block) {
                        $toTest->addObject($block);
                    }
                    $toDos->addObjectsFromArray($funcDesc->toDoBlocks());
                }
            }
        }
        return new NorfDocTestSearchResult($toTest, $toDos);
    }

}


class NorfDocTestSearchResult
{

    private $_toTest;
    private $_toDos;

    function __construct($toTest, $toDos)
    {
        $this->_toTest = $toTest;
        $this->_toDos = $toDos;
    }

    function blocksToTest()
    {
        return $this->_toTest;
    }

    function toDoBlocks()
    {
        return $this->_toDos;
    }

}

