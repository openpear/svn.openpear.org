<?php
/**
 * @package HatenaSyntax
 * @author anatoo<anatoo@nequal.jp>
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @version $Id$
 */

class HatenaSyntax_Util
{
    static function normalizeList(Array $data)
    {
        return HatenaSyntax_Tree::make($data);
    }

    static function segment(PEG_IParser $p)
    {
        return PEG::callbackAction(array('HatenaSyntax_Util', 'normalizeLineSegment'), $p);
    }
    
    static function normalizeLineSegment(Array $data)
    {
        for ($ret = array(), $i = 0, $len = count($data); $i < $len; $i++) {
            if (is_string($data[$i])) {
                for ($str = $data[$i++]; $i < $len && is_string($data[$i]); $i++) {
                    $str .= $data[$i];
                }
                $ret[] = $str;
                if ($i < $len) $ret[] = $data[$i];
            }
            else {
                $ret[] = $data[$i];
            }
        }
        return $ret;
    }
    
    static function processListItem(Array $li)
    {
        $ret = array();
        $ret['level'] = count($li[0]) - 1;
        $ret['value'] = array(end($li[0]), $li[1]);
        
        return $ret;
    }
}