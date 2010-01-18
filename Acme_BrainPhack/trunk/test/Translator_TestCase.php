<?php
/*
 *   Copyright (c) 2010 msakamoto-sf <msakamoto-sf@users.sourceforge.net>
 *
 *   Licensed under the Apache License, Version 2.0 (the "License");
 *   you may not use this file except in compliance with the License.
 *   You may obtain a copy of the License at
 *
 *       http://www.apache.org/licenses/LICENSE-2.0
 *
 *   Unless required by applicable law or agreed to in writing, software
 *   distributed under the License is distributed on an "AS IS" BASIS,
 *   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *   See the License for the specific language governing permissions and
 *   limitations under the License.*
 */

/**
 * @package Acme_BrainPhack
 * @author msakamoto-sf <msakamoto-sf@users.sourceforge.net>
 * @version $Id$
 */

/**
 * requires
 */
require_once('Acme/BrainPhack/Translator.php');

class Translator_TestCase extends UnitTestCase
{

    function setUp()
    {
        mb_internal_encoding('UTF-8');
    }

    // {{{ testOriginalBrainFuck()

    function testOriginalBrainFuck()
    {
        $bpt =& new Acme_BrainPhack_Translator();

        $map = array(
            '+' => '+',
            '-' => '-',
            '>' => '>',
            '<' => '<',
            '.' => '.',
            ',' => ',',
            '[' => '[',
            ']' => ']',
            );
        $src = '';
        $r = $bpt->translate($map, $src);
        $this->assertIdentical($r, '');

        $src = '?';
        $r = $bpt->translate($map, $src);
        $this->assertIdentical($r, '');

        $src = '][,.<>-+';
        $r = $bpt->translate($map, $src);
        $this->assertIdentical($r, $src);

        $src = '+-><.,[]';
        $r = $bpt->translate($map, $src);
        $this->assertIdentical($r, $src);
    }

    // }}}
    // {{{ testSimpleAlphabetTable()

    function testSimpleAlphabetTable()
    {
        $bpt =& new Acme_BrainPhack_Translator();

        $map = array(
            '+' => 'A',
            '-' => 'B',
            );
        $r = $bpt->translate($map, 'A');
        $this->assertIdentical($r, '+');
        $r = $bpt->translate($map, 'B');
        $this->assertIdentical($r, '-');
        $r = $bpt->translate($map, 'AB');
        $this->assertIdentical($r, '+-');
        $r = $bpt->translate($map, 'BA');
        $this->assertIdentical($r, '-+');

        $map = array(
            '+' => 'ABC',
            '-' => 'DEF',
            );
        $src = '';
        $r = $bpt->translate($map, $src);
        $this->assertIdentical($r, '');

        $src = '?';
        $r = $bpt->translate($map, $src);
        $this->assertIdentical($r, '');

        $src = 'ABC';
        $r = $bpt->translate($map, $src);
        $this->assertIdentical($r, '+');

        $src = 'DEF';
        $r = $bpt->translate($map, $src);
        $this->assertIdentical($r, '-');

        $src = 'ABCDEF';
        $r = $bpt->translate($map, $src);
        $this->assertIdentical($r, '+-');

        $src = 'DEFABC';
        $r = $bpt->translate($map, $src);
        $this->assertIdentical($r, '-+');

        $src = '?    ABC - DEF //---';
        $r = $bpt->translate($map, $src);
        $this->assertIdentical($r, '+-');

        $src = ' ABC - CDE - DEF  DEF  FABC';
        $r = $bpt->translate($map, $src);
        $this->assertIdentical($r, '+--+');
    }

    // }}}
    // {{{ testMultibyte()

    function testMultibyte()
    {
        $bpt =& new Acme_BrainPhack_Translator();

        $map = array(
            '+' => 'あ',
            '-' => 'い',
            );
        $src = '';
        $r = $bpt->translate($map, $src);
        $this->assertIdentical($r, '');

        $src = '?';
        $r = $bpt->translate($map, $src);
        $this->assertIdentical($r, '');

        $src = 'あ';
        $r = $bpt->translate($map, $src);
        $this->assertIdentical($r, '+');

        $src = 'い';
        $r = $bpt->translate($map, $src);
        $this->assertIdentical($r, '-');

        $src = 'あい';
        $r = $bpt->translate($map, $src);
        $this->assertIdentical($r, '+-');

        $src = 'いあ';
        $r = $bpt->translate($map, $src);
        $this->assertIdentical($r, '-+');

        $src = '?    あ - い //---';
        $r = $bpt->translate($map, $src);
        $this->assertIdentical($r, '+-');

        $src = ' あ - い  い  かあ';
        $r = $bpt->translate($map, $src);
        $this->assertIdentical($r, '+--+');
    }

    // }}}
    // {{{ testMultiWords()

    function testMultiWords()
    {
        $bpt =& new Acme_BrainPhack_Translator();

        $map = array(
            '+' => array('A', 'B'),
            '-' => 'C',
            );
        $r = $bpt->translate($map, '');
        $this->assertIdentical($r, '');
        $r = $bpt->translate($map, 'A');
        $this->assertIdentical($r, '+');
        $r = $bpt->translate($map, 'B');
        $this->assertIdentical($r, '+');
        $r = $bpt->translate($map, 'AB');
        $this->assertIdentical($r, '++');
        $r = $bpt->translate($map, 'BA');
        $this->assertIdentical($r, '++');
        $r = $bpt->translate($map, 'C');
        $this->assertIdentical($r, '-');
        $r = $bpt->translate($map, 'AC');
        $this->assertIdentical($r, '+-');
        $r = $bpt->translate($map, 'BC');
        $this->assertIdentical($r, '+-');
        $r = $bpt->translate($map, 'CA');
        $this->assertIdentical($r, '-+');
        $r = $bpt->translate($map, 'CB');
        $this->assertIdentical($r, '-+');
        $r = $bpt->translate($map, 'CC');
        $this->assertIdentical($r, '--');
        $r = $bpt->translate($map, 'ABC');
        $this->assertIdentical($r, '++-');
        $r = $bpt->translate($map, 'CBA');
        $this->assertIdentical($r, '-++');

        $map = array(
            '+' => array('AB', 'CD'),
            '-' => array('EF', 'GH'),
        );
        $r = $bpt->translate($map, '? EF - A - AB .. GH G H CD /');
        $this->assertIdentical($r, '-+-+');
    }

    // }}}
    // {{{ testMultiWordsByMultibyte()

    function testMultiWordsByMultibyte()
    {
        $bpt =& new Acme_BrainPhack_Translator();

        $map = array(
            '+' => array('あ', 'い'),
            '-' => 'う',
            );
        $r = $bpt->translate($map, '');
        $this->assertIdentical($r, '');
        $r = $bpt->translate($map, 'あ');
        $this->assertIdentical($r, '+');
        $r = $bpt->translate($map, 'い');
        $this->assertIdentical($r, '+');
        $r = $bpt->translate($map, 'あい');
        $this->assertIdentical($r, '++');
        $r = $bpt->translate($map, 'いあ');
        $this->assertIdentical($r, '++');
        $r = $bpt->translate($map, 'う');
        $this->assertIdentical($r, '-');
        $r = $bpt->translate($map, 'あう');
        $this->assertIdentical($r, '+-');
        $r = $bpt->translate($map, 'いう');
        $this->assertIdentical($r, '+-');
        $r = $bpt->translate($map, 'うあ');
        $this->assertIdentical($r, '-+');
        $r = $bpt->translate($map, 'うい');
        $this->assertIdentical($r, '-+');
        $r = $bpt->translate($map, 'うう');
        $this->assertIdentical($r, '--');
        $r = $bpt->translate($map, 'あいう');
        $this->assertIdentical($r, '++-');
        $r = $bpt->translate($map, 'ういあ');
        $this->assertIdentical($r, '-++');

        $map = array(
            '+' => array('あい', 'うえ'),
            '-' => array('かき', 'くけ'),
        );
        $r = $bpt->translate($map, '? かき - あ - あい .. くけ く け うえ');
        $this->assertIdentical($r, '-+-+');
    }

    // }}}
    // {{{ testMisa()

    /**
     * @see http://homepage2.nifty.com/kujira_niku/okayu/misa.html
     */
    function testMisa()
    {
        $bpt =& new Acme_BrainPhack_Translator();

        $map = array(
            '>' => array('>', '→', '～', 'ー'),
            '>' => array('<', '←', '★', '☆'),
            '+' => array('+', 'あ', 'ぁ', 'お', 'ぉ'),
            '-' => array('-', 'っ', 'ッ'),
            '.' => array('.', '！'),
            ',' => array(',', '？'),
            '[' => array('[', '「', '『'),
            ']' => array(']', '」', '』'),
        );

        $src =<<<TEXT
あおあおあお「～ああぁおおぉ☆っ」
ここまでで６ｘ８＝４８＝アスキーコードとすれば文字列０！
あ！あ！あ！あ！あ！あ！あ！あ！あ！
TEXT;
        $r = $bpt->translate($map, $src);
        $this->assertIdentical($r, '++++++[++++++>-].+.+.+.+.+.+.+.+.+.');
    }

    // }}}
}

/**
 * Local Variables:
 * mode: php
 * coding: iso-8859-1
 * tab-width: 4
 * c-basic-offset: 4
 * c-hanging-comment-ender-p: nil
 * indent-tabs-mode: nil
 * End:
 * vim: set expandtab tabstop=4 shiftwidth=4:
 */
