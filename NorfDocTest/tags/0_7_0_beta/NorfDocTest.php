<?php

require_once dirname(__FILE__) . '/NorfComparator.php';
require_once dirname(__FILE__) . '/NorfGenerator.php';
require_once dirname(__FILE__) . '/NorfRange.php';
require_once dirname(__FILE__) . '/NorfBounded.php';
require_once dirname(__FILE__) . '/NorfArray.php';
require_once dirname(__FILE__) . '/NorfDictionary.php';
require_once dirname(__FILE__) . '/NorfProcedure.php';
require_once dirname(__FILE__) . '/NorfStringScanner.php';
require_once dirname(__FILE__) . '/NorfPHPToken.php';
require_once dirname(__FILE__) . '/NorfPHPScanner.php';
require_once dirname(__FILE__) . '/NorfStringUtilities.php';
require_once dirname(__FILE__) . '/NorfPathUtilities.php';
require_once dirname(__FILE__) . '/NorfClassUtilities.php';

require_once dirname(__FILE__) . '/NorfDocTestToken.php';
require_once dirname(__FILE__) . '/NorfDocTestScanner.php';
require_once dirname(__FILE__) . '/NorfDocTestParser.php';
require_once dirname(__FILE__) . '/NorfDocTestLogger.php';

require_once dirname(__FILE__) . '/NorfDocTestBlock.php';
require_once dirname(__FILE__) . '/NorfDocTestBlockStore.php';
require_once dirname(__FILE__) . '/NorfDocTestClassDescription.php';
require_once dirname(__FILE__) . '/NorfDocTestMethodDescription.php';
require_once dirname(__FILE__) . '/NorfDocTestFunctionDescription.php';
require_once dirname(__FILE__) . '/NorfDocTestModule.php';
require_once dirname(__FILE__) . '/NorfDocTestModuleGroup.php';
require_once dirname(__FILE__) . '/NorfDocTestContext.php';
require_once dirname(__FILE__) . '/NorfDocTestRequest.php';
require_once dirname(__FILE__) . '/NorfDocTestSearchElement.php';
require_once dirname(__FILE__) . '/NorfDocTestEvaluationResult.php';
require_once dirname(__FILE__) . '/NorfDocTestDifferences.php';
require_once dirname(__FILE__) . '/NorfJSONSerialization.php';


class NorfDocTest
{

    private static $_cmdLine = true;

    static function isCommandLine()
    {
        return self::$_cmdLine;
    }

    static function setCommandLine($flag)
    {
        self::$_cmdLine = $flag;
    }

    static function _handleDocTestParseError($e)
    {
        print 'NorfDocTest: File \'' . $e->path() . '\': ';
        if ($e->lineNumber() !== null)
            print 'Line ' . $e->lineNumber() . ": ";
        print $e->getMessage() . "\n";
        exit(1);
    }

    static function _handleJSONParseError($e)
    {
        print 'NorfDocTest: File \'' . $e->path() . '\': ';
        if ($e->lineNumber() !== null)
            print 'Line ' . $e->lineNumber() . ": ";
        print $e->getMessage() . "\n";
        exit(1);
    }

    function __construct()
    {
        $this->_store = new NorfDocTestModule();
    }

    function testStore()
    {
        return $this->_store;
    }

    function loadFileAtPath($path)
    {
        $this->_store->loadFileAtPath($path);
    }

    function executeAllTests($logger=null)
    {
        if (!$logger)
            $logger = new NorfDocTestDefaultLogger();

        $resultStore = new NorfDocTestEvaluationResultStore();
        $logger->beginTests();
        $this->_executeAllTestsOfFunctions($resultStore, $logger);
        $this->_executeAllTestsOfClass($resultStore, $logger);
        $logger->endTests();
        $logger->finish($resultStore);
    }

    function _executeAllTestsOfFunctions($resultStore, $logger)
    {
        foreach ($this->_store->testedFunctions() as $func) {
            if ($func->hasTests()) {
                $logger->beginTestingFunction($func);
                foreach ($func->tests() as $test)
                    $this->_executeTest($resultStore, $logger,
                                        null, $func, $test);
                $logger->endTestingFunction($func);
            }
        }
    }

    function _executeAllTestsOfClass($resultStore, $logger)
    {
        foreach ($this->_store->testedClasses() as $class) {
            if ($class->hasTestsWithClass($class)) {
                $logger->beginTestingClass($class);

                // tests of class
                foreach ($class->tests() as $test)
                    $this->_executeTest($resultStore, $logger,
                                        $class, null, $test);

                // tests of each methods
                foreach ($class->submodules() as $meth) {
                    if ($meth->hasTestsWithClass($class)) {
                        $logger->beginTestingMethod($meth);
                        foreach ($meth->allTests() as $test)
                            $this->_executeTest($resultStore, $logger,
                                                $class, $meth, $test);
                        $logger->endTestingMethod($meth);
                    }
                }
                $logger->endTestingClass($class);
            }
        }
    }

    function _executeTest($resultStore, $logger, $class, $meth, $test)
    {
        if ($test->isToDo()) {
            print "todo -- " . $test->fullName()."\n";
            $resultStore->_incrementToDoCountForModule($class);
        } else if ($test->canTestWithClass($class)) {
            $logger->beginExecutingTest($test, $class);
            $result = $test->execute($class);
            $resultStore->_addResult($result);
            $logger->logResult($result);
            $logger->endExecutingTest($test, $class);
        }
    }

}


class NorfDocTestCommand extends NorfDocTest
{

    function loadFileAtPath($path)
    {
        try {
            parent::loadFileAtPath($path);
        } catch (NorfPHPScannerError $e) {
            $this->_printScannerError($e);
        }
    }

    function executeAllTests($logger=null)
    {
        parent::executeAllTests($logger);
    }

    function _printScannerError($e)
    {
        $msg = '';
        if ($e->_path)
            $msg .= $e->path() . ': ';
        if ($e->_lineNum)
            $msg .= 'line ' . $e->lineNumber() . ': ';
        if ($e->_charColNum)
            $msg .= 'column ' . $e->characterColumnNumber() . ': ';
        $msg .= get_class($e) . ":\n    " . $e->getMessage();
        $this->printErrorMessage($msg);
    }

    function printErrorMessage($msg)
    {
        print "NorfDocTest: $msg\n";
        exit(1);
    }

}

