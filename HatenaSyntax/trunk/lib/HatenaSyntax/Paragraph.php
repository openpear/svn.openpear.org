<?php
/**
 * @package HatenaSyntax
 * @author anatoo<anatoo@nequal.jp>
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @version $Id$
 */

class HatenaSyntax_Paragraph implements PEG_IParser
{
    protected $parser, $line;

    function __construct(PEG_IParser $lineelt)
    {
        $this->parser = PEG::callbackAction(
            array($this, 'map'),
            PEG::anything()
        );
        $this->line = PEG::callbackAction(
            array($this, 'normalize'),
            PEG::many($lineelt)
        );
    }

    function parse(PEG_IContext $context)
    {
        return $this->parser->parse($context);
    }

    function map($line)
    {
        return $this->line->parse(PEG::context($line));
    }

    /**
     * @param Array $rest 
     * @return Array
     */
    function normalize(Array $rest)
    {
        $ret = array();
        
        while ($rest) {
            list($elt, $rest) = $this->segment($rest);
            $ret[] = $elt;
        }

        return $ret;
    }

    /**
     * @param Array
     * @return Array array($elt, $rest)
     */
    function segment(Array $p)
    {
        $first = array_shift($p);
        $rest = $p;

        if (!is_string($first)) {
            return array($first, $rest);
        }

        $str = $first;
        while ($rest) {
            if (is_string($rest[0])) {
                $str .= array_shift($rest);
            }
            else {
                return array($str, $rest);
            }
        }
        return array($str, array());
    }
}
