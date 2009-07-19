<?php
/**
 * Date_Japanese_Era tests

 * PHP version 5.2
 *
 * Copyright (c) 2009 Heavens hell, All rights reserved.
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
 * @category  Date
 * @package   Date_Japanese
 * @version   $id$
 * @copyright 2009 Heavens hell
 * @author    Heavens hell <heavenshell.jp@gmail.com>
 * @license   New BSD License
 * @link      http://search.cpan.org/~miyagawa/Date-Japanese-Era-0.06/
 */
$rootPath = dirname(dirname(__FILE__));
$libPath  = $rootPath . DIRECTORY_SEPARATOR . 'src';
set_include_path($libPath . PATH_SEPARATOR . get_include_path());

require_once 'lime.php';
require_once 'Date/Japanese/Era.php';

$t = new lime_test(null, new lime_output_color());

$tests = array(
    array(array(2001, 9, 1), array('平成', 13)),
    array(array(1989, 1, 8), array('平成', 1)),
    array(array(1989, 1, 7), array('昭和', 64)),
    array(array(1977, 9, 12), array('昭和', 52)),
    array(array(1926, 12, 25), array('昭和', 1)),
    array(array(1926, 12, 24), array('taishou', 15)),
    array(array(1912, 7, 30), array('taishou', 1)),
    array(array(1912, 7, 29), array('meiji', 45)),
    array(array(1873, 1, 1), array('meiji', 6)),
    array(array(1868, 9, 8), array('meiji', 1))
);

foreach ($tests as $key => $val) {
    $e1   = new Date_Japanese_Era($val[0]);
    $name = $val[1][0];
    if (preg_match('/^[a-zA-Z]+$/', $name)) {
        $t->ok($e1->nameAscii === $name, 'Gregorian to Japanese era (ASCII)');
    } else {
        $t->ok($e1->name === $name, 'Gregorian to Japanese era');
    }
    $eraYear = $val[1][1];
    $t->ok($e1->year === $eraYear, 'Japanese era');

    $e2 = new Date_Japanese_Era(array($name, $eraYear));
    $t->ok($e2->gregorianYear === $val[0][0], 'Japanese era to Gregorian');
}

// fail tests
$tests = array(
    array(array(), 'Invalid number of arguments: 0'),
    array(array('xxx', 1), 'Unknown era name: '),
    array(array('慶応', 12), 'Unknown era name: 慶応'),
    array(array('昭和', 65), 'Invalid combination of era and year: 昭和-65'),
    array(array(2001, -1, -1), 'Invalid date.'),
);
foreach ($tests as $key => $val) {
    try {
        $e3 = new Date_Japanese_Era($val[0]);
    } catch (Date_Japanese_Era_Exception $e) {
        $t->ok($e->getMessage() === $val[1], 'various ways to fail');
    }
}

// Override era table
$data = array(
    '慶応' => array('keiou', 1865, 4, 7, 1868, 9, 7),
    '明治' => array('meiji', 1868, 9, 8, 1912, 7, 29),
    '大正' => array('taishou', 1912, 7, 30, 1926, 12, 24),
    '昭和' => array('shouwa', 1926, 12, 25, 1989, 1, 7),
    '平成' => array('heisei', 1989, 1, 8, 2038, 12, 31)
);
Date_Japanese_Era_Table::$ERA_TABLE = $data;
$e4 = new Date_Japanese_Era(array(1865, 4, 7));
$t->ok($e4->name === key($data), 'Gregorian to Japanese era');
$t->ok($e4->year === 1, 'Gregorian to Japanese era');


