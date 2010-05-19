<?php

/**
 * wozozoがんばって！
 *
 * @author Katsuhiro Ogawa <fivestar@nequal.jp>
 */
class Wozozo_PropelUtil
{
    public static function toArray($obj, $keyType = BasePeer::TYPE_FIELDNAME)
    {
        $array = array();

        if (is_array($obj)) {
            foreach ($obj as $k => $v) {
                $array[$k] = self::toArray($v, $keyType);
            }
        } else if ($obj instanceof BaseObject) {
            $array = $obj->toArray($keyType);
        }

        return $array;
    }
}
