<?php

/**
 * wozozoがんばって！
 *
 * @author Katsuhiro Ogawa <fivestar@nequal.jp>
 */
class Wozozo_PropelUtil
{
    public static function toArray($obj, $keyType = BasePeer::TYPE_FIELDNAME, $called = null)
    {
        $array = array();

        if (is_array($obj)) {
            foreach ($obj as $k => $v) {
                $array[$k] = self::toArray($v, $keyType, $called);
            }
        } else if ($obj instanceof BaseObject) {
            $array = $obj->toArray($keyType);

            $relations = $obj->getPeer()->getTableMap()->getRelations();
            foreach ($relations as $name => $relation) {
                if (strtolower($name) === strtolower($called)) {
                    continue;
                }

                $method = 'get'.$name;
                if (in_array($relation->getType(), array(RelationMap::ONE_TO_MANY))) {
                    $method .= 's';
                }

                if (method_exists($obj, $method)) {
                    $array[$name] = self::toArray($obj->$method(), $keyType, get_class($obj));
                }
            }
        }

        return $array;
    }
}
