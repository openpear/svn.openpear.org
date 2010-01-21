<?php
/**
 * @package HatenaSyntax
 * @author anatoo<anatoo@nequal.jp>
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @version $Id$
 */

class HatenaSyntax_LineElement implements PEG_IParser
{
    protected $bracket, $footnote;

    function __construct(PEG_IParser $bracket, PEG_IParser $footnote)
    {
        list($this->bracket, $this->footnote) = func_get_args();
    }

    function parse(PEG_IContext $context)
    {
        if ($context->eos()) {
            return PEG::failure();
        }

        $char = $context->readElement();

        if ($char === '[') {
            $offset = $context->tell() - 1;
            $context->seek($offset);

            $result = $this->bracket->parse($context);

            if ($result instanceof PEG_Failure) {
                $context->seek($offset + 1);
                return $char;
            }

            return $result;
        }

        if ($context->eos()) {
            return $char;
        }

        $char .= $context->readElement();

        if ($char === '((') {

            $oldoffset = $context->tell() - 2;
            $context->seek($oldoffset);

            $result = $this->footnote->parse($context);

            if ($result instanceof PEG_Failure) {
                $context->seek($oldoffset + 2);
                return $char;
            }

            return $result;
        }

        return $char;
    }
}
