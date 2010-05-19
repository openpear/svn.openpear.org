<?php

/**
 * wozozoがんばって！
 *
 * @author Katsuhiro Ogawa <fivestar@nequal.jp>
 */
class Wozozo_PropelUtil
{
    public static function toArray($obj, $keyType = BasePeer::TYPE_FIELDNAME, &$models = array())
    {
        $array = array();

        if (is_array($obj)) {
            foreach ($obj as $k => $v) {
                $array[$k] = self::toArray($v, $keyType, $models);
            }
        } else if ($obj instanceof BaseObject) {
            $models[strtolower(get_class($obj))] = strtolower(get_class($obj));

            $array = $obj->toArray($keyType);

            $relations = $obj->getPeer()->getTableMap()->getRelations();
            foreach ($relations as $name => $relation) {
                if (in_array(strtolower($name), $models)) {
                    continue;
                }

                $method = 'get'.$name;
                if (in_array($relation->getType(), array(RelationMap::ONE_TO_MANY))) {
                    $method .= 's';
                }

                if (method_exists($obj, $method)) {
                    $array[$name] = self::toArray($obj->$method(), $keyType, $models);
                }
            }
        }

        return $array;
    }
}
