<?php
/**
 * @package PEG
 * @author anatoo<anatoo@nequal.jp>
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @version $Id$
 */

/**
 * どのような文字にでもヒットするパーサ
 *
 */
class PEG_Anything implements PEG_IParser
{
    function __construct() { }
    function parse(PEG_IContext $context)
    {
        if ($context->eos()) return PEG_Failure;
        return $context->read(1);
    }
}