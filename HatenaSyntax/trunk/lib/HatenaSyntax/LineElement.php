<?php
/**
 * @package HatenaSyntax
 * @author anatoo<anatoo@nequal.jp>
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @version $Id$
 */

class HatenaSyntax_LineElement implements PEG_IParser
{
    protected $table;

    function __construct(PEG_IParser $bracket, PEG_IParser $footnote)
    {
        $this->table = array(
            '[' => PEG::choice($bracket, PEG::anything()),
            '(' => PEG::choice($footnote, PEG::anything())
        );
    }

    function parse(PEG_IContext $context)
    {
        if ($context->eos()) {
            return PEG::failure();
        }

        $char = $context->readElement();

        if ($char === '[' || $char === '(') {
            $offset = $context->tell() - 1;
            $context->seek($offset);

            $result = $this->table[$char]->parse($context);

            if ($result instanceof PEG_Failure) {
                $context->seek($offset + 1);
                return $char;
            }

            return $result;
        }

        return $char;
    }
}
