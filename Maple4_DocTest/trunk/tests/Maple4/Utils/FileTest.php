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
 * @category   Utils
 * @package    Maple4_Utils
 * @copyright  2008 Maple Project
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @since      File available since Release 0.2.0
 */

require_once('PHPUnit/Framework.php');
require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/src/Maple4/Utils/File.php');

/**
 * ファイル関連のユーティリティークラスのテスト
 *
 * @category   Utils
 * @package    Maple4_Utils
 * @author     TAKAHASHI Kunihiko <kunit@maple-project.com>
 * @copyright  2008 Maple Project
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @since      Class available since Release 0.2.0
 */
class Maple4_Utils_FileTest extends PHPUnit_Framework_TestCase
{
    private $instance;
    private $baseDir;

    /**
     * テストの下準備
     */
    public function setUp()
    {
        $this->instance = new Maple4_Utils_File();
        $this->baseDir = dirname(dirname(dirname(dirname(__FILE__))));
    }

    /**
     * テストの後始末
     */
    public function tearDown()
    {
        $this->instance = null;
    }

    /**
     * ディレクトリの区切り文字をOS独自のものに置き換えできるか？
     */
    public function testFixDirectorySeparator()
    {
        $pathname = '/foo/bar';

        if (strstr(PHP_OS, "WIN")) {
            $expected = '\\foo\\bar';
        } else {
            $expected = '/foo/bar';
        }

        $this->assertEquals($expected, $this->instance->fixDirectorySeparator($pathname));
    }

    /**
     * 末尾のディレクトリの区切り文字が必ず削除されている状態に
     * できるか？
     */
    public function testRemoveTailSlash()
    {
        $pathname = '/foo/bar';
        if (strstr(PHP_OS, "WIN")) {
            $expected = '\\foo\\bar';
        } else {
            $expected = '/foo/bar';
        }

        $this->assertEquals($expected, $this->instance->removeTailSlash($pathname));

        $pathname = '/foo/bar/';
        if (strstr(PHP_OS, "WIN")) {
            $expected = '\\foo\\bar';
        } else {
            $expected = '/foo/bar';
        }

        $this->assertEquals($expected, $this->instance->removeTailSlash($pathname));
    }

    /**
     * 末尾のディレクトリの区切り文字が必ず付加されている状態に
     * できるか？
     */
    public function testAddTailSlash()
    {
        $pathname = '/foo/bar';
        if (strstr(PHP_OS, "WIN")) {
            $expected = '\\foo\\bar\\';
        } else {
            $expected = '/foo/bar/';
        }

        $this->assertEquals($expected, $this->instance->addTailSlash($pathname));

        $pathname = '/foo/bar/';
        if (strstr(PHP_OS, "WIN")) {
            $expected = '\\foo\\bar\\';
        } else {
            $expected = '/foo/bar/';
        }

        $this->assertEquals($expected, $this->instance->addTailSlash($pathname));
    }

    /**
     * 先頭のディレクトリの区切り文字が必ず削除されている状態に
     * できるか？
     */
    public function testRemoveHeadSlash()
    {
        $pathname = '/foo/bar';
        if (strstr(PHP_OS, "WIN")) {
            $expected = 'foo\\bar';
        } else {
            $expected = 'foo/bar';
        }

        $this->assertEquals($expected, $this->instance->removeHeadSlash($pathname));

        $pathname = 'foo/bar';
        if (strstr(PHP_OS, "WIN")) {
            $expected = 'foo\\bar';
        } else {
            $expected = 'foo/bar';
        }

        $this->assertEquals($expected, $this->instance->removeHeadSlash($pathname));
    }

    /**
     * 先頭のディレクトリの区切り文字が必ず付加されている状態に
     * できるか？
     */
    public function testAddHeadSlash()
    {
        $pathname = '/foo/bar';
        if (strstr(PHP_OS, "WIN")) {
            $expected = '\\foo\\bar';
        } else {
            $expected = '/foo/bar';
        }

        $this->assertEquals($expected, $this->instance->addHeadSlash($pathname));

        $pathname = 'foo/bar';
        if (strstr(PHP_OS, "WIN")) {
            $expected = '\\foo\\bar';
        } else {
            $expected = '/foo/bar';
        }

        $this->assertEquals($expected, $this->instance->addHeadSlash($pathname));
    }

    /**
     * 処理対象外の管理が正しく行えるか？
     */
    public function testIgnore()
    {
        $pathname = '/foo/bar';

        $this->assertEquals(false, $this->instance->isIgnore($pathname));

        $this->instance->setIgnore($pathname);
        $this->assertEquals(true, $this->instance->isIgnore($pathname));

        $this->instance->clearIgnore();
        $this->assertEquals(false, $this->instance->isIgnore($pathname));

        $list = array(
            '/foo',
            '/foo/bar',
        );

        $this->instance->setIgnore($list);
        $this->assertEquals(true, $this->instance->isIgnore($pathname));
    }

    /**
     * テストの基準ディレクトリを見つけることができるか？
     */
    public function testSearchBasePathname()
    {
        $pathname = $this->baseDir . '/fixtures';

        if (strstr(PHP_OS, "WIN")) {
            $this->assertTrue(preg_match('|^[A-Za-z]+:$|', $this->instance->searchBasePathname($pathname)) === 1);
        } else {
            $expected = null;
            $this->assertEquals($expected, $this->instance->searchBasePathname($pathname));
        }

        $pathname = $this->baseDir . '/fixtures/Maple4/DocTest/testcases';
        $expected = $this->instance->fixDirectorySeparator($pathname);

        $this->assertEquals($expected, $this->instance->searchBasePathname($pathname));

        $pathname = $this->baseDir . '/fixtures/Maple4/DocTest/testcases/Test2/Test5/';
        $expected = $this->instance->fixDirectorySeparator($this->baseDir . '/fixtures/Maple4/DocTest/testcases');

        $this->assertEquals($expected, $this->instance->searchBasePathname($pathname));

        $pathname = $this->baseDir . '/fixtures/Maple4/DocTest/testcases/Test3/Test6/Test8/Test9Test.php';
        $expected = $this->instance->fixDirectorySeparator($this->baseDir . '/fixtures/Maple4/DocTest/testcases');

        $this->assertEquals($expected, $this->instance->searchBasePathname($pathname));
    }

    /**
     * 指定したディレクトリ直下のディレクトリおよびファイルリストが
     * 取得できるか？
     */
    public function testLs()
    {
        $pathname = $this->baseDir . '/fixtures/Maple4/DocTest/testcases';
        $this->instance->setIgnore(Maple4_Utils_File::MARKER_FILENAME);
        $this->instance->setIgnore('\.svn');

        $expected = array(array(), array());

        $this->assertEquals($expected, $this->instance->ls(''));

        $expected = array(
            array(
                $this->instance->fixDirectorySeparator("{$pathname}/Test1"),
                $this->instance->fixDirectorySeparator("{$pathname}/Test2"),
                $this->instance->fixDirectorySeparator("{$pathname}/Test3"),
            ),
            array(
                $this->instance->fixDirectorySeparator("{$pathname}/Test1Test.php"),
                $this->instance->fixDirectorySeparator("{$pathname}/Test2Test.php"),
                $this->instance->fixDirectorySeparator("{$pathname}/Test3Test.php"),
            ),
        );

        $this->assertEquals($expected, $this->instance->ls($pathname));

        $this->instance->setIgnore("Test1Test.php");

        $expected = array(
            array(
                $this->instance->fixDirectorySeparator("{$pathname}/Test1"),
                $this->instance->fixDirectorySeparator("{$pathname}/Test2"),
                $this->instance->fixDirectorySeparator("{$pathname}/Test3"),
            ),
            array(
                $this->instance->fixDirectorySeparator("{$pathname}/Test2Test.php"),
                $this->instance->fixDirectorySeparator("{$pathname}/Test3Test.php"),
            ),
        );

        $this->assertEquals($expected, $this->instance->ls($pathname));
    }

    /**
     * 指定したディレクトリ直下のファイルリストが取得できるか？
     */
    public function testFind()
    {
        $pathname = $this->baseDir . '/fixtures/Maple4/DocTest/testcases';
        $this->instance->setIgnore(Maple4_Utils_File::MARKER_FILENAME);
        $this->instance->setIgnore('\.svn');

        $expected = array(
            $this->instance->fixDirectorySeparator("{$pathname}/Test1Test.php"),
            $this->instance->fixDirectorySeparator("{$pathname}/Test2Test.php"),
            $this->instance->fixDirectorySeparator("{$pathname}/Test3Test.php"),
        );

        $actual = $this->instance->find($pathname);
        sort($expected);
        sort($actual);
        $this->assertEquals($expected, $actual);

        $expected = array(
            $this->instance->fixDirectorySeparator("{$pathname}/Test2Test.php"),
        );

        $this->assertEquals($expected, $this->instance->find($pathname, '|Test2|'));

        $expected = array(
            "Test1Test.php",
            "Test2Test.php",
            "Test3Test.php",
        );

        $actual = $this->instance->find($pathname, null, 'basename');
        sort($expected);
        sort($actual);
        $this->assertEquals($expected, $actual);

        $expected = array(
            "Test3Test.php",
        );

        $this->assertEquals($expected, $this->instance->find($pathname, '|Test3|', 'basename'));
    }

    /**
     * 指定したディレクトリ以下のファイルリストが取得できるか？
     */
    public function testFindRecursive()
    {
        $pathname = $this->baseDir . '/fixtures/Maple4/DocTest/testcases';
        $this->instance->setIgnore(Maple4_Utils_File::MARKER_FILENAME);
        $this->instance->setIgnore('\.svn');

        $expected = array(
            $this->instance->fixDirectorySeparator("{$pathname}/Test1/Test4Test.php"),
            $this->instance->fixDirectorySeparator("{$pathname}/Test1Test.php"),
            $this->instance->fixDirectorySeparator("{$pathname}/Test2/Test5/Test7Test.php"),
            $this->instance->fixDirectorySeparator("{$pathname}/Test2/Test5Test.php"),
            $this->instance->fixDirectorySeparator("{$pathname}/Test2Test.php"),
            $this->instance->fixDirectorySeparator("{$pathname}/Test3/Test6/Test8/Test9Test.php"),
            $this->instance->fixDirectorySeparator("{$pathname}/Test3/Test6/Test8Test.php"),
            $this->instance->fixDirectorySeparator("{$pathname}/Test3/Test6Test.php"),
            $this->instance->fixDirectorySeparator("{$pathname}/Test3Test.php"),
        );

        $actual = $this->instance->findRecursive($pathname);
        sort($expected);
        sort($actual);
        $this->assertEquals($expected, $actual);

        $expected = array(
            $this->instance->fixDirectorySeparator("{$pathname}/Test2/Test5/Test7Test.php"),
            $this->instance->fixDirectorySeparator("{$pathname}/Test2/Test5Test.php"),
            $this->instance->fixDirectorySeparator("{$pathname}/Test2Test.php"),
        );

        $actual = $this->instance->findRecursive($pathname, '|Test2|');
        sort($expected);
        sort($actual);
        $this->assertEquals($expected, $actual);

        $expected = array(
            "Test1Test.php",
            "Test2Test.php",
            "Test3Test.php",
            "Test4Test.php",
            "Test5Test.php",
            "Test6Test.php",
            "Test7Test.php",
            "Test8Test.php",
            "Test9Test.php",
        );

        $actual = $this->instance->findRecursive($pathname, null, 'basename');
        sort($expected);
        sort($actual);
        $this->assertEquals($expected, $actual);
    }

    /**
     * ファイルが正しく削除できるか？
     */
    public function testUnlink()
    {
        $pathname = $this->baseDir . '/tmp/unlinkTest.txt';

        @unlink($pathname);

        $this->assertEquals(false, file_exists($pathname));

        touch($pathname);

        $this->assertEquals(true, file_exists($pathname));

        $this->assertEquals(true, $this->instance->unlink($pathname));

        $this->assertEquals(false, file_exists($pathname));
    }

    /**
     * 正しくディレクトリの操作ができるか？
     */
    public function testDir()
    {
        $pathname = $this->baseDir . '/tmp/dirTest';

        $this->instance->removeDir($pathname);
        $this->assertEquals(false, file_exists($pathname));

        $this->assertEquals(true, $this->instance->makeDir($pathname));

        $this->assertEquals(true, file_exists($pathname));
        $this->assertEquals(true, is_dir($pathname));

        $this->assertEquals(true, $this->instance->removeDir($pathname));

        $this->assertEquals(false, file_exists($pathname));

        $this->instance->makeDir("{$pathname}/foo/bar");
        $this->assertEquals(true, file_exists("{$pathname}/foo/bar"));

        touch("{$pathname}/foo/bar/baz.txt");

        $this->assertEquals(true, $this->instance->removeDir($pathname));
        $this->assertEquals(false, file_exists($pathname));
        $this->assertEquals(false, file_exists("{$pathname}/foo"));
        $this->assertEquals(false, file_exists("{$pathname}/foo/bar"));
    }

    /**
     * 正しくファイルの操作ができるか？
     */
    public function testFile()
    {
        $pathname = $this->baseDir . '/tmp/fileTest.txt';

        $expected = <<<EOT
11111
22222
33333
EOT;

        $this->instance->unlink($pathname);
        $this->instance->write($pathname, $expected);
        $this->assertEquals($expected, $this->instance->read($pathname));
        $this->instance->unlink($pathname);
    }
}