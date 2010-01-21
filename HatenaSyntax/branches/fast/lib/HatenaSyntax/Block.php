<?php
/**
 * @package HatenaSyntax
 * @author anatoo<anatoo@nequal.jp>
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @version $Id$
 */

class HatenaSyntax_Block implements PEG_IParser
{
    protected 
        $lineTable,
        $paragraphCheckTable,
        $firstCharTable,
        $paragraph;

    function __construct(HatenaSyntax_Locator $locator)
    {
        /*
         * 当該パーサへのディスパッチは以下の順で行われる。
         *
         * 1. lineTableからのディスパッチ
         * 2. paragraphCheckTableからのディスパッチ
         * 3. firstCharTableからのディスパッチ
         * 4. パラグラフ
         *
         */

        $this->lineTable = array(
            '' => $locator->emptyParagraph,
            '>>' => PEG::choice(
                $locator->blockquote,
                $locator->paragraph
            ),
            '>|' => PEG::choice(
                $locator->pre,
                $locator->paragraph
            ),
            '>||' => PEG::choice(
                $locator->superpre,
                $locator->paragraph
            ),
            '[:contents]' => $locator->tableOfContents
        );

        // 行の最初の一文字が存在し、かつこの配列のキー以外だった場合
        // 必ずパラグラフが来る
        $this->paragraphCheckTable = array(
            ':' => true,
            '>' => true,
            '|' => true,
            '+' => true,
            '-' => true,
            '*' => true
        );

        $this->firstCharTable = array(
            '*' => $locator->header,
            '+' => $locator->list,
            '-' => $locator->list,
            '|' => PEG::choice(
                $locator->table,
                $locator->paragraph
            ),
            ':' => PEG::choice(
                $locator->definitionList, 
                $locator->paragraph
            ),
            '>' => PEG::choice(
                $locator->superpre,
                $locator->blockquote,
                $locator->paragraph
            )
        );

        $this->paragraph = $locator->paragraph;
    }

    function parse(PEG_IContext $context)
    {
        if ($context->eos()) {
            return PEG::failure();
        }

        $line = $context->readElement();
        $context->seek($context->tell() - 1);

        // 行でディスパッチ
        if (isset($this->lineTable[$line])) {
            return $this->lineTable[$line]->parse($context);
        }

        $char = substr($line, 0, 1);

        // 最初の文字でパラグラフかどうか判断
        if (!isset($this->paragraphCheckTable[$char])) {
            return $this->paragraph->parse($context);
        }

        if (isset($this->firstCharTable[$char])) {
            return $this->firstCharTable[$char]->parse($context);
        }

        return $this->paragraph->parse($context);
    }
}