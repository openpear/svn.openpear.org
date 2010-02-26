<?php
/**
 * @package HatenaSyntax
 * @author anatoo<anatoo@nequal.jp>
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @version $Id$
 */

class HatenaSyntax_Quote implements PEG_IParser
{
    protected $child, $parser;

    function __construct(PEG_IParser $child)
    {
        $this->child = $child;

        $this->parser = PEG::seq(
            PEG::callbackAction(array($this, 'mapHeader'), PEG::anything()),
            PEG::many(PEG::subtract($this->child, '<<')),
            PEG::drop('<<')
        );
    }

    function parse(PEG_IContext $context)
    {
        return $this->parser->parse($context);
    }

    function mapHeader($line)
    {
        if (substr($line, 0, 1) !== '>') {
            return PEG::failure();
        }

        if (!preg_match('#^>(|https?://[^>]+)>$#', $line, $matches)) {
            return PEG::failure();
        }

        return $matches[1] === '' ? false : $matches[1]; 
    }
}
