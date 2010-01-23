<?php
/**
 * @package HatenaSyntax
 * @author anatoo<anatoo@nequal.jp>
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @version $Id$
 */

class HatenaSyntax_Header implements PEG_IParser
{
    protected $child, $parser;

    function __construct(PEG_IParser $elt)
    {
        $this->child = PEG::many($elt);
        $this->parser = PEG::callbackAction(array($this, 'map'), PEG::anything());
    }

    function parse(PEG_IContext $context)
    {
        return $this->parser->parse($context);
    }

    function map($line)
    {
        if (strpos($line, '*') === 0) {
            preg_match('/^\**/', substr($line, 1), $matches);
            $level = strlen($matches[0]);
            $body = $this->child->parse(PEG::context((string)substr($line, $level + 1)));
            return array($level, $body);
        }

        return PEG::failure();
    }
}
