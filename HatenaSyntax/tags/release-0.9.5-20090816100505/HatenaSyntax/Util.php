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
        return self::filterLevel(self::lower(self::levels($data), $data));
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
        $ret[] = end($li[0]);
        $ret[] = count($li[0]) - 1;
        $ret[] = $li[1];
        
        
        return $ret;
    }
    
    static protected function filterLevel($struct)
    {
        foreach ($struct as &$node) 
            $node = is_string($node[0]) ? array($node[0], $node[2]) : self::filterLevel($node);
        return $struct;
    }
    
    static protected function lower(Array $levels, Array $data)
    {
        $level = array_pop($levels);
        
        for ($i = 0, $ret = array(), $len = count($data); $i < $len; $i++) {
            if ($data[$i][1] <= $level) {
                $ret[] = $data[$i];
            }
            else {
                for ($arr = array($data[$i++]); $i < $len && $data[$i][1] > $level; $i++) {
                    $arr[] = $data[$i];
                }
                $ret[] = self::lower($levels, $arr);
                if ($i < $len) $ret[] = $data[$i];
            }
        }
        
        return $ret;
    }
    
    static protected function levels(Array $data)
    {
        $levels = array();
        foreach ($data as $li) $levels[$li[1]] = true;
        $levels = array_keys($levels);
        rsort($levels, SORT_NUMERIC);
        return $levels;
    }
}