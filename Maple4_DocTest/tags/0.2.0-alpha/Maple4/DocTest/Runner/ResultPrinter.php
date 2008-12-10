<?php
/**
 * PHP versions 5
 *
 * Copyright (c) 2008 Maple Project, All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Sebastian Bergmann nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category   Testing
 * @package    Maple4_DocTest
 * @copyright  2008 Maple Project
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @since      File available since Release 0.2.0
 */

require_once 'PHPUnit/TextUI/ResultPrinter.php';
require_once 'PHPUnit/Util/Filter.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__, 'PHPUNIT');

/**
 * PHPUnit3の出力結果に色をつけるためのクラス 
 *
 * @category   Testing
 * @package    Maple4_DocTest
 * @author     TAKAHASHI Kunihiko <kunit@maple-project.com>
 * @copyright  2008 Maple Project
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @since      Class available since Release 0.2.0
 */
class Maple4_DocTest_Runner_ResultPrinter extends PHPUnit_TextUI_ResultPrinter
{
    const LINE_WIDTH = 80;

    const STYLE_BOLD = 1;
    const FG_WHITE = 37;
    const BG_RED = 41;
    const BG_GREEN = 42;
    const BG_MAGENTA = 45;

    /**
     * PHPUnitの出力部分にフックする
     *
     * @param  array   $defects
     * @param  integer $count
     * @param  string  $type
     */
    protected function printDefects(array $defects, $count, $type)
    {
        if ($count == 0) {
            return;
        }

        if (($type == 'error') || ($type == 'failure')) {
            $bgColor = self::BG_RED;
        } else {
            $bgColor = self::BG_MAGENTA;
        }

        $str = sprintf(
            "There %s %d %s%s:\n",

            ($count == 1) ? 'was' : 'were',
            $count,
            $type,
            ($count == 1) ? '' : 's'
        );

        $this->write($this->convert($bgColor, $str));

        $i = 1;

        foreach ($defects as $defect) {
            $this->printDefect($defect, $i++);
        }
    }

    /**
     * PHPUnitの出力部分にフックする
     *
     * @param  PHPUnit_Framework_TestResult  $result
     */
    protected function printFooter(PHPUnit_Framework_TestResult $result)
    {
        if ($result->wasSuccessful() &&
            $result->allCompletlyImplemented() &&
            $result->noneSkipped()) {
            $str = sprintf(
                "OK (%d test%s)\n",

                count($result),
                (count($result) == 1) ? '' : 's'
            );

            $this->write($this->convert(self::BG_GREEN, $str));
        } else if ((!$result->allCompletlyImplemented() ||
                    !$result->noneSkipped()) &&
                    $result->wasSuccessful()) {
            $str = sprintf(
                "OK, but incomplete or skipped tests!\n" .
                "Tests: %d%s%s.\n",

                count($result),
                $this->getCountString($result->notImplementedCount(), 'Incomplete'),
                $this->getCountString($result->skippedCount(), 'Skipped')
            );

            $this->write("\n" . $this->convert(self::BG_MAGENTA, $str));
        } else {
            $str = sprintf(
                "FAILURES!\n" .
                "Tests: %d%s%s%s%s.\n",

                count($result),
                $this->getCountString($result->failureCount(), 'Failures'),
                $this->getCountString($result->errorCount(), 'Errors'),
                $this->getCountString($result->notImplementedCount(), 'Incomplete'),
                $this->getCountString($result->skippedCount(), 'Skipped')
            );

            $this->write("\n" . $this->convert(self::BG_RED, $str));
        }
    }

    /**
     * 指定された色コードに対するエスケープシーケンスを発行
     *
     * @param integer $code 色コード
     * @param string $str 表示文字列
     * @return string 装飾済みの文字列
     * @access private
     */
    private function convert($code, $str)
    {
        $lines = preg_split("|\n|", rtrim($str));

        $result = null;

        foreach ($lines as $line) {
            if (strlen($line) < self::LINE_WIDTH) {
                $line = $line . str_repeat(' ', self::LINE_WIDTH - strlen($line));
            }

            $codes = array();
            $codes[] = self::STYLE_BOLD;
            $codes[] = self::FG_WHITE;
            $codes[] = $code;

            $result .= sprintf("\033[%sm%s\033[0m\n", implode(';', $codes), $line);
        }

        return $result;
   }
}