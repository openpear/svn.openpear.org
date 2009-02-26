<?php

interface NorfDocTestLoggerVisitor
{

    function visitDefaultLogger($logger);
    function visitHTMLLogger($logger);

}


class NorfDocTestLogger
{

    private $_indentSize = 4;

    static function acceptLogger($logger, $target)
    {
        if (NorfClassUtilities::isKindOfClass
            ($logger, 'NorfDocTestDefaultLogger'))
            $target->visitDefaultLogger($logger);
        else if (NorfClassUtilities::isKindOfClass
            ($logger, 'NorfDocTestHTMLLogger'))
            $target->visitHTMLLogger($logger);
    }

    function willEvaluate($context) {}
    function didEvaluate($context, $resultSet) {}
    function blockWillEvaluate($context, $block) {}
    function blockDidEvaluate($context, $block, $result) {}

    function __construct()
    {
        $this->_indent = 0;
    }

    function indentSize()
    {
        return $this->_indentSize;
    }

    function setIndentSize($size)
    {
        $this->_indentSize($size);
    }

    function indentLevel()
    {
        return $this->_indent;
    }

    function indent()
    {
        $this->_indent++;
    }

    function dedent()
    {
        $this->_indent--;
    }

    function write($s=null)
    {
        for ($i = 0; $i < $this->_indent; $i++)
            print str_repeat(' ', $this->_indentSize);
        if ($s)
            print $s;
        flush();
    }

    function writeln($s=null)
    {
        for ($i = 0; $i < $this->_indent; $i++)
            print str_repeat(' ', $this->_indentSize);
        if ($s)
            print $s;
        print "\n";
        flush();
    }

    function printStatusMarkWithResult($result)
    {
        if ($result->isPassed())
            print '.';
        else if ($result->isFailed())
            print 'F';
        else if ($result->isSkipped())
            print 'S';
        else
            print 'E';
    }

    function printResultSummary($resultSet)
    {
        print $resultSet->testCount() . " tests, " .
            $resultSet->failureCount() . " failures, " .
            $resultSet->errorCount() . " errors, " .
            $resultSet->skipCount() . " skips, " .
            $resultSet->toDoCount() . " todos\n";
        $this->printElapsedTime($resultSet);
    }

    function printElapsedTime($resultSet)
    {
        printf("Total time: %.3f seconds\n", $resultSet->elapsedTime());
    }

}


class NorfDocTestDefaultLogger extends NorfDocTestLogger
{

    private $_width = 80;

    function width()
    {
        return $this->_width;
    }

    function setWidth($value)
    {
        $this->_width = $value;
    }

    function willEvaluate($context)
    {
        print 'Testing ';
        if ($name = $context->request()->name())
            print $name;
        print "\n\n";
    }

    function didEvaluate($context, $resultSet)
    {
        print "\n\n";
        if ($context->request()->loggers()->count() == 1)
            $this->printFailures($resultSet);
        $this->printResultSummary($resultSet);
        print "\n";
    }

    function printFailures($resultSet)
    {
        $i = 1;
        $fails = 1;
        $errors = 1;
        $skips = 1;
        foreach ($resultSet->results() as $result) {
            if (!$result->isPassed()) {
                $this->write("$i) ");
                if ($result->isFailed())
                    $this->write("Failure $fails");
                else if ($result->isError())
                    $this->write("Error $errors");
                else if ($result->isSkipped())
                    $this->write("Skip $skips");
                $this->writeln();
                $this->indent();

                $block = $result->block();
                $this->writeln($block->blockStore()->signature() . ': ' .
                               $block->title());
                $this->writeln($result->failureMessage());
                if ($diffs = $result->differences())
                    $diffs->acceptLogger($this);
                $this->writeln();

                $this->dedent();
                $i++;
                $fails++;
                $errors++;
                $skips++;
            }
        }
    }

    function blockWillEvaluate($context, $block)
    {
    }

    function blockDidEvaluate($context, $block, $result)
    {
        $this->printStatusMarkWithResult($result);
    }


}


class NorfDocTestHTMLLogger extends NorfDocTestLogger
{

    function __construct($output)
    {
        $this->_output = $output;
    }

    function outputDirectoryPath()
    {
        $this->_output;
    }

    function setOutputDirectoryPath($path)
    {
        $this->_output = $path;
    }

    function willEvaluate($context)
    {
    }

    function blockDidEvaluate($context, $block, $result)
    {
    }

    function didEvaluate($context, $resultSet)
    {
        print "Creating report into HTML files in \"$this->_output\"...\n";
        $this->writeToHTMLFiles($context, $resultSet);
    }

    function writeToHTMLFiles($context, $resultSet)
    {
        if (!file_exists($this->_output))
            mkdir($this->_output);
        $this->writeIndexFile($context, $resultSet);
        $this->writeMainNavigationFile($context, $resultSet);
        $this->writeSubnavigationFiles($context, $resultSet);
        $this->writeOverviewFile($context, $resultSet);
        //$this->writeAllFunctionFile($resultSet);
    }

    private function writeIndexFile($context, $resultSet)
    {
        $path = NorfPathUtilities::
            stringByAppendingPathComponent($this->_output, 'index.html');
        $this->_name = $context->request()->name();
        if (!$this->_name)
            $this->_name = 'NorfDocTest';
        $s = "<html><head><title>$this->_name Report</title></head>" .
            '<frameset cols="25%,*"><frameset rows="35%,*">' .
            '<frame src="MainNavigation.html"/>' .
            '<frame name="Subnavigation" src="AllClasses.html"/>' .
            '</frameset>' .
            '<frame name="Content" src="About.html"/>' .
            '</frameset></html>';
        $f = fopen($path, 'w');
        fwrite($f, $s);
        fclose($f);
    }

    private function writeMainNavigationFile($context, $resultSet)
    {
        $s = "<html><head><title>$this->_name Report</title></head>" .
            "<body><h2>$this->_name</h2>" .
            '<a href="Overview.html" target="Content" ' .
            'class="SectionLink">Overview</a><br/>';

        $s .= '<a href="AllTests.html" target="Content">All Tests</a><br/>';
        $s .= '<a href="AllFunctions.html" target="Content">All Functions</a><br/>';
        $s .= '<a href="AllClasses.html" target="Content">All Classes</a><br/>';

        $s .= '<h3>Files</h3>';
        foreach ($resultSet->allModules() as $module) {
            $path = $module->relativePath();
            $HTMLPath = NorfPathUtilities::
                stringByAppendingPathComponent('Files', $path) . '.html';
            $s .= '<a href="' . $HTMLPath . '">' . $path . '</a><br/>';
        }

        $path = NorfPathUtilities::
            stringByAppendingPathComponent($this->_output,
                                           'MainNavigation.html');
        $f = fopen($path, 'w');
        fwrite($f, $s);
        fclose($f);
    }

    private function writeSubnavigationFiles($context, $resultSet)
    {
        $this->writeAllClassesFile($context, $resultSet);
    }

    private function writeAllClassesFile($context, $resultSet)
    {
        $s = '<a href="AllPassedClasses.html" target="Content">' .
            'All Passed Classes</a><br/>' .
            '<a href="AllFailedClasses.html" target="Content">' .
            'All Failed Classes</a><br/>' .
            '<a href="AllErroredClasses.html" target="Content">' .
            'All Errored Classes</a><br/>' .
            '<a href="AllSkippedClasses.html" target="Content">' .
            'All Skipped Classes</a><br/>' .
            '<a href="AllToDoClasses.html" target="Content">' .
            'All ToDo Classes</a><br/>' .
            '<h3>All Classes</a></h3>';
        foreach ($resultSet->allClassDescriptions() as $classDesc) {
            $s .= '<a href="' . $classDesc->name() . '.html" target="Content">' .
                $classDesc->name() . '</a><br/>';
            //$this->_writeResultFileForClass($classDesc, $resultSet);
        }
        $s .= '</body></html>';

        $path = NorfPathUtilities::
            stringByAppendingPathComponent($this->_output,
                                           'AllClasses.html');
        $f = fopen($path, 'w');
        fwrite($f, $s);
        fclose($f);
    }
    
    private function writeOverviewFile($context, $resultSet)
    {
        $s = "<html><body><h2>$this->_name</h2>";

        $s .= '<table border=1>' .
            '<tr><td>Total Tests</td><td>' . $resultSet->testCount() .
            '</td></tr><tr><td>Total Functions</td><td>' .
            $resultSet->allFunctionDescriptions()->count() .
            '</td></tr><tr><td>Total Classes</td><td>' .
            $resultSet->allClassDescriptions()->count() .
            '</td></tr><tr><td>Total Elapsed Time</td>' .
            '<td>' . sprintf("%.3f", $resultSet->elapsedTime()) . ' seconds</td>' .
            '</table>';

        $s .= '<h3>Files</h3>';
        $s .= '<table border=1><tr><th>Path</th><th>Passed</th>' .
            '<th>Failed</th><th>Errored</th><th>Skipped</th>' .
            '<th>ToDos</th><th>Elapsed Time</th></tr>';
        foreach ($resultSet->allModules() as $module) {
            $s .= '<tr><td>' . $module->relativePath() . '</td>' .
                '</tr>';
        }
        $s .= '</table>';

        $s .= '</body></html>';
        $path = NorfPathUtilities::
            stringByAppendingPathComponent($this->_output,
                                           'Overview.html');
        $f = fopen($path, 'w');
        fwrite($f, $s);
        fclose($f);
    }

    private function writeAllFunctionFile($resultSet)
    {
        $beginTable = '<table border=1><tr><th>Test</th><th>Description</th></tr>';
        $s = '';
        foreach ($resultSet->allFunctionDescriptions() as $func) {
            $s .= '<a name="' . $func->name() . '"/>';
            $s .= '<h2>Function ' . $func->name() . '</h2>';
        }

        $path = NorfPathUtilities::
            stringByAppendingPathComponent($this->_output,
                                           'AllFunctions.html');
        $f = fopen($path, 'w');
        fwrite($f, $s);
    }

    function _writeResultFileForClass($class, $resultSet)
    {
        $beginTable = '<table border=1><tr><th>Method</th><th>Test</th><th>Description</th></tr>';

        $ref = $class->reflector();
        $s = '<html><body>';
        $s .= '<h2>Class ' . $class->name() . '</h2><dl>';
        if ($super = $ref->getParentClass())
            $s .= '<dt>Extends</dt><dd>' . $super->getName() .
                '</dd></dt>';
        $s .= '<dt>Defined in</dt><dd>' . $ref->getFileName() .
            '</dd></dt>';

        $s .= '<dt>Tests/Failures/Errors/Skips/ToDos</dt><dd>';
        $s .= $resultSet->passCount() . '/' .
            $resultSet->failureCount() . '/' .
            $resultSet->errorCount() . '/' .
            $resultSet->skipCount() .  '/' .
            $resultSet->toDoCount() . '</dd>';
        $s .= '</dl>';

        if ($count = $resultSet->errorCount()) {
            $s .= "<h3>Errors ($count)</h3>";
            $s .= $beginTable;
            foreach ($resultSet->results() as $result) {
                if ($result->status() == NorfDocTestEvaluationResult::ERROR) {
                    $s .= '<tr><td><code>' .
                        $result->test()->parentModule()->name() .
                        '</code></td><td><code>' .
                        $result->test()->name() .
                        '</code></td><td>' .
                        htmlspecialchars($result->failureMessage()) .
                        '</td></tr>';
                }
            }
            $s .= '</table>';
        }

        if ($count = $resultSet->failureCount()) {
            $s .= "<h3>Failures ($count)</h3>";
            $s .= $beginTable;
            foreach ($resultSet->results() as $result) {
                if ($result->status() == NorfDocTestEvaluationResult::FAILURE) {
                    $s .= '<tr><td><code>' .
                        $result->test()->parentModule()->name() .
                        '</code></td><td><code>' .
                        $result->test()->name() .
                        '</code></td><td>' .
                        htmlspecialchars($result->failureMessage()) .
                        '</td></tr>';
                }
            }
            $s .= '</table>';
        }

        if ($count = $resultSet->passCount()) {
            $s .= "<h3>Passes ($count)</h3>";
            $s .= $beginTable;
            foreach ($resultSet->results() as $result) {
                if ($result->status() == NorfDocTestEvaluationResult::PASS) {
                    $s .= '<tr><td><code>' .
                        $result->test()->parentModule()->name() .
                        '</code></td><td><code>' .
                        $result->test()->name() .
                        '</code></td><td></td></tr>';
                }
            }
            $s .= '</table>';
        }

        $s .= '</body></html>';

        $path = NorfPathUtilities::
            stringByAppendingPathComponent($this->_output,
                                           $class->name() . '.html');
        $f = fopen($path, 'w');
        fwrite($f, $s);
        fclose($f);
    }

}

