<?php

class NorfDocTestRequest
{

    private $_name;
    private $_consoleWidth = 80;
    private $_loggers;
    private $_testingClassNames;
    private $_ignoringClassNames;
    private $_classPatterns;
    private $_testingMethNames;
    private $_ignoringMethNames;
    private $_methPatterns;
    private $_testingFuncNames;
    private $_ignoringFuncNames;
    private $_funcPatterns;

    function __construct($name=null)
    {
        $this->_name = $name;
        $this->_loggers = new NorfArray(new NorfDocTestDefaultLogger());
        $this->_testingClassNames = new NorfArray();
        $this->_ignoringClassNames = new NorfArray();
        $this->_classPatterns = new NorfArray();
        $this->_testingMethNames = new NorfArray();
        $this->_ignoringMethNames = new NorfArray();
        $this->_methPatterns = new NorfArray();
        $this->_testingFuncNames = new NorfArray();
        $this->_ignoringFuncNames = new NorfArray();
        $this->_funcPatterns = new NorfArray();
    }

    function name()
    {
        return $this->_name;
    }

    function setName($name)
    {
        $this->_name = $name;
    }

    function consoleWidth()
    {
        return $this->_consoleWidth;
    }

    function setConsoleWidth($width)
    {
        $this->_consoleWidth = $width;
        foreach ($this->_loggers as $logger) {
            if (NorfClassUtilities::isKindOfClass
                ($logger, 'NorfDocTestDefaultLogger'))
                $logger->setWidth($width);
        }
    }
    
    function loggers()
    {
        return $this->_loggers;
    }

    function addLogger($logger)
    {
        $this->_loggers->addObject($logger);
    }

    function removeLogger($logegr)
    {
        $this->_loggers->removedObject($logger);
    }

    function testingClassNames()
    {
        return $this->_testingClassNames;
    }

    function ignoringClassNames()
    {
        return $this->_ignoringClassNames;
    }

    function addTestingClassName($name)
    {
        if ($this->_ignoringClassNames->containsObject($name))
            $this->_ignoringClassNames->removeObject($name);
        else
            $this->_testingClassNames->addObject($name);
    }

    function addIgnoringClassName($name)
    {
        if ($this->_testingClassNames->containsObject($name))
            $this->_testingClassNames->removeObject($name);
        else
            $this->_ignoringClassNames->addObject($name);
    }

    function classPatterns()
    {
        return $this->_classPatterns;
    }

    function addClassPattern($pattern)
    {
        $this->_classPatterns->addObject($pattern);
        return $pattern;
    }

    function removeClassPattern($pattern)
    {
        $this->_removePatterns->removeObject($pattern);
    }

    function testingMethodNamesForClassNamed($name)
    {
        $names = $this->_testingMethNames->objectForKey($name);
        if ($names === null) {
            $names = new NorfArray();
            $this->_testingMethNames->setObjectForKey($names, $name);
            if (!$this->_testingClassNames->containsObject($name))
                $this->addTestingClassName($name);
        }
        return $names;
    }

    function ignoringMethodNamesForClassNamed($name)
    {
        $names = $this->_ignoringMethNames->objectForKey($name);
        if ($names === null) {
            $names = new NorfArray();
            $this->_ignoringMethNames->setObjectForKey($names, $name);
            if (!$this->_ignoringClassNames->containsObject($name))
                $this->addIgnoringClassName($name);
        }
        return $names;
    }

    function addTestingMethodNameForClassNamed($methName, $className)
    {
        $exNames = $this->ignoringMethodNamesForClassNamed($className);
        if ($exNames->containsObject($methName))
            $exNames->removeObject($methName);
        else {
            $inNames = $this->testingMethodNamesForClassNamed($className);
            $inNames->addObject($methName);
        }
    }

    function addIgnoringMethodNamedForClassNamed($methName, $className)
    {
        $inNames = $this->testingMethodNamesForClassNamed($className);
        if ($inNames->containsObject($methName))
            $inNames->removeObject($methName);
        else {
            $exNames = $this->ignoringMethodNamesForClassNamed($className);
            $exNames->addObject($methName);
        }
    }

    function methodPatternsForClassNamed($name)
    {
        $patterns = $this->_methPatterns->objectForKey($name);
        if ($patterns === null) {
            $patterns = new NorfArray();
            $this->_methPatterns->setObjectForKey($patterns, $name);
            $inNames = $this->testingMethodNamesForClassNamed($className);
            $inNames->addObjectIfAbsert($className);
         }
        return $patterns;
    }

    function addMethodPatternForClassNamed($pattern, $name)
    {
        $this->methodPatternsForClassNamed($name)->addObject($pattern);
    }

    function removeMethodPatternForClassNamed($pattern, $name)
    {
        $this->methodPatternsForClassNamed($name)->removeObject($pattern);
    }

    function testingFunctionNames()
    {
        return $this->_testingFuncNames;
    }

    function ignoringFunctionNames()
    {
        return $this->_ignoringFuncNames;
    }

    function addTestingFunctionName($name)
    {
        if ($this->_ignoringFuncNames->containsObject($name))
            $this->_ignoringFuncNames->removeObject($name);
        else
            $this->_testingFuncNames->addObject($name);
    }

    function addIgnoringFunctionName($name)
    {
        if ($this->_testingFuncNames->containsObject($name))
            $this->_testingFuncNames->removeObject($name);
        else
            $this->_ignoringFuncNames->addObject($name);
    }

    function functionPatterns()
    {
        return $this->_funcPatterns;
    }

    function addFunctionPattern($pattern)
    {
        $this->_funcPatterns->addObject($pattern);
    }

    function removeFunctionPattern($pattern)
    {
        $this->_removePatterns->removeObject($pattern);
    }

    function classSearchElement()
    {
        $els = new NorfArray();

        foreach ($this->_testingClassNames as $name) {
            $el = new NorfDocTestNameSearchElement($name);
            $els->addObject($el);
        }

        foreach ($this->_ignoringClassNames as $name) {
            $el = new NorfDocTestNameSearchElement($name);
            $el = NorfDocTestCompoundSearchElement::
                notSearchElementWithSubsearchElement($el);
            $els->addObject($el);
        }

        foreach ($this->_classPatterns as $pattern) {
            $el = new NorfDocTestNamePatternSearchElement($pattern);
            $els->addObject($el);
        }

        if ($els->isEmpty())
            return NorfDocTestBooleanSearchElement::trueSearchElement();
        else
            return NorfDocTestCompoundSearchElement::
                andSearchElementWithSubsearchElements($els);
    }

    function methodSearchElementDictionary()
    {
        $dict = new NorfDictionary();

        foreach ($this->_testingClassNames as $className) {
            $els = new NorfArray();

            foreach ($this->_testingMethNames->objectForKey($className)
                     as $methName) {
                $el = new NorfDocTestNameSearchElement($methName);
                $els->addObject($el);
            }

            foreach ($this->_ignoringMethNames->objectForKey($className)
                     as $methName) {
                $el = new NorfDocTestNameSearchElement($methName);
                $el = NorfDocTestCompoundSearchElement::
                    notSearchElementWithSubsearchElement($el);
                $els->addObject($el);
            }

            foreach ($this->_methPatterns->objectForKey($className)
                     as $pattern) {
                $el = new NorfDocTestNamePatternSearchElement($pattern);
                $els->addObject($el);
            }

            if ($els->isEmpty())
                $el = NorfDocTestBooleanSearchElement::trueSearchElement();
            else
                $el = NorfDocTestCompoundSearchElement::
                    andSearchElementWithSubsearchElements($els);
            $dict->setObjectForKey($el, $className);
        }
        return $dict;
    }

    function functionSearchElement()
    {
        $els = new NorfArray();

        foreach ($this->_testingFuncNames as $name) {
            $el = new NorfDocTestNameSearchElement($name);
            $els->addObject($el);
        }

        foreach ($this->_ignoringFuncNames as $name) {
            $el = new NorfDocTestNameSearchElement($name);
            $el = NorfDocTestCompoundSearchElement::
                notSearchElementWithSubsearchElement($el);
            $els->addObject($el);
        }

        foreach ($this->_funcPatterns as $pattern) {
            $el = new NorfDocTestNamePatternSearchElement($pattern);
            $els->addObject($el);
        }

        if ($els->isEmpty())
            return NorfDocTestBooleanSearchElement::trueSearchElement();
        else
            return NorfDocTestCompoundSearchElement::
                andSearchElementWithSubsearchElements($els);
    }

}

