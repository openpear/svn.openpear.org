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
            list($level, $rest) = $this->levelAndRest($line);
            list($name, $body) = $this->nameAndBody($rest);

            $body = $this->child->parse(PEG::context($body));

            return array($level, $name, $body);
        }

        return PEG::failure();
    }

    protected function levelAndRest($line)
    {
        $level = 0;
        $line = (string)substr($line, 1);

        for ($i = 0, $len = strlen($line); $i < $len; $i++) {
            if ($line[$i] === '*') {
                $level++;
            } else {
                break;
            }
        }

        return array($level, substr($line, $level));
    }

    protected function nameAndBody($rest)
    {
        if (preg_match('/^(.*?)\*/', $rest, $matches)) {
            return array($matches[1], (string)substr($rest, strlen($matches[0])));
        }

        return array(false, $rest);
    }
}
