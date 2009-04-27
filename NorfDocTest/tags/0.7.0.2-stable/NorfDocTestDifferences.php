<?php

abstract class NorfDocTestDifferences implements NorfDocTestLoggerVisitor
{

    function acceptLogger($logger)
    {
        NorfDocTestLogger::acceptLogger($logger, $this);
    }

}


class NorfDocTestStringDifferences extends NorfDocTestDifferences
{

    private $_expected;
    private $_return;
    private $_diffs;

    function __construct($expected, $return, $diffs)
    {
        $this->_expected = $expected;
        $this->_return = $return;
        $this->_diffs = $diffs;
    }

    function visitDefaultLogger($logger)
    {
        $marks = '';
        $fixes = '';
        foreach ($this->_diffs as $diff) {
            $len = strlen($diff[1]);
            if ($diff[0] == $diff[1]) {
                $marks .= str_repeat(' ', $len);
                $fixes .= str_repeat(' ', $len);
            } else {
                $marks .= str_repeat('^', $len);
                $fixLen = strlen($diff[0]);
                if ($fixLen <= $len) {
                    $fixes .= $diff[0];
                    $fixes .= str_repeat(' ', $len - $fixLen);
                } else if ($len >= 3) {
                    $fixes .= substr($diff[0], 0, $len-2) . '..';
                } else
                    $fixes .= substr($diff[0], 0, $len);
            }
        }

        $max = max(strlen($this->_expected), strlen($this->_return));
        $width = $logger->width() - $logger->indentLevel() *
            $logger->indentSize() - 4;
        for ($i = 0, $n = $max / $width; $i < $n; $i++) {
            $logger->writeln('=== ' .
                             substr($this->_expected, $i * $width, $width));
            $logger->writeln('*** ' .
                             substr($this->_return, $i * $width, $width));
            $logger->writeln('    ' .
                             substr($marks, $i * $width, $width));
            $logger->writeln('    ' .
                             substr($fixes, $i * $width, $width));
        }
    }

    function visitHTMLLogger($logger)
    {
    }

}

