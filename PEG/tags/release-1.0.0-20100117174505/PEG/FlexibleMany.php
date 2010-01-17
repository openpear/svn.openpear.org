<?php
/**
 * @package PEG
 * @author anatoo<anatoo@nequal.jp>
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @version $Id$
 */

class PEG_FlexibleMany implements PEG_IParser
{
    protected $parser, $following;
    function __construct(PEG_IParser $parser, PEG_IParser $following)
    {
        list($this->parser, $this->following) = func_get_args();
    }

    function parse(PEG_IContext $context)
    {
        $result_arr = $offset_arr = array();
        $result_tmp = $offset_tmp = null;

        do {
            $offset_tmp = $context->tell();
            $result_tmp = $this->parser->parse($context);

            if ($result_tmp instanceof PEG_Failure) {
                $context->seek($offset_tmp);
                break;
            }

            $result_arr[] = $result_tmp;
            $offset_arr[] = $offset_tmp;
            
        } while(!$context->eos());

        for (;;) {
            $following_result = $this->following->parse($context);

            if ($following_result instanceof PEG_Failure) {
                if (!$result_arr) {
                    return PEG::failure();
                }

                array_pop($result_arr);
                $context->seek(array_pop($offset_arr));
            }
            else {
                return array($result_arr, $following_result);
            }
        }
    }

    protected function filterNull(Array $arr)
    {
        $ret = array();
        foreach ($arr as $elt) {
            if (!is_null($elt)) $ret[] = $elt;
        }
        return $ret;
    }
}
