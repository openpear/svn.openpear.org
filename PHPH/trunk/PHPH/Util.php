<?php

class PHPH_Util
{
	private static $native_types = array(
		"bool" => "bool",
		"double" => "double",
		"float" => "double",
		"long" => "long",
		"int" => "long",
		"string" => "string",
		"array" => "array"
	);

	public static function getZendDeclareConst($class, $key, $value)
	{
		$prefix = "zend_declare_class_constant";
		$type = self::getType($value);
		switch ($type) {
		case "bool":
		case "double":
		case "long":
		case "string":
			$key = self::escape($key);
			$value = self::escape($value);
			return sprintf("%s_%s(ce_%s, %s, sizeof(%s)-1, %s);\n", $prefix, $type, $class, $key, $key, $value);
		}
		return null;
	}

	public static function getZendDeclareProperty($class, $key, $value, $flag)
	{
		$prefix = "zend_declare_property";
		$type = self::getType($value);
		switch ($type) {
		case "bool":
		case "double":
		case "long":
		case "string":
			$key = self::escape($key);
			$value = self::escape($value);
			return sprintf("%s_%s(ce_%s, %s, sizeof(%s)-1, %s, %s);\n", $prefix, $type, $class, $key, $key, $value, $flag);
		case "array":
			$zkey = "z_".$key;
			$key = self::escape($key);
			$zval = self::var2zval($value, $zkey);
			return $zval.sprintf("%s(ce_%s, %s, sizeof(%s)-1, %s, %s);\n", $prefix, $class, $key, $key, $zkey, $flag);
		case "null":
			$key = self::escape($key);
			return sprintf("%s_null(ce_%s, %s, sizeof(%s)-1, %s);\n", $prefix, $type, $class, $key, $key, $flag);
		}
		return null;
	}

	public static function getZendRegisterConstant($ns, $key, $value)
	{
		$type = self::getType($value);
		switch ($type) {
		case "bool":
		case "double":
		case "long":
		case "string":
			$type = strtoupper($type);
			$key = self::escape($key);
			$value = self::escape($value);
			if (isset($ns)) {
				$ns = self::escape($ns);
				return sprintf("REGISTER_NS_%s_CONSTANT(%s, %s, %s, CONST_PERSISTENT | CONST_CS);\n", $type, $ns, $key, $value);
			} else {
				return sprintf("REGISTER_%s_CONSTANT(%s, %s, CONST_PERSISTENT | CONST_CS);\n", $type, $key, $value);
			}
		}
		return null;
	}

	public static function var2zval($value, $key)
	{
		$str = "";
		$type = self::getType($value);
		$key = str_replace(" ", "", $key);
		$str .= sprintf("zval *%s;\n", $key);
		$str .= sprintf("MAKE_STD_ZVAL(%s);\n", $key);
		switch ($type) {
		case "bool":
		case "double":
		case "long":
			if (is_bool($value)) {
				$valule = (int)$value;
			}
			$str .= sprintf("ZVAL_%s(%s, %s);\n", strtoupper($type), $key, $value);
			break;
		case "string":
			$str .= sprintf("ZVAL_STRING(%s, %s, 0);\n", $key, self::escape($value));
			break;
		case "array":
			$str .= sprintf("array_init(%s);\n", $key);
			if (self::isAssoc($value)) {
				// assoc
				foreach ($value as $k=>$v) {
					$k_type = self::getType($k);
					$v_type = self::getType($v);
					if ($k_type=="long") {
						// assoc long key
						switch ($v_type) {
						case "bool":
						case "double":
						case "long":
							$v = self::escape($v);
							$str .= sprintf("add_index_%s(%s, %d, %s);\n", $v_type, $key, $k, $v);
							break;
						case "string":
							$v = self::escape($v);
							$str .= sprintf("add_index_string(%s, %d, %s, 0);\n", $key, $k, $v);
							break;
						case "array":
							$key2 = str_replace(" ", "", sprintf("%s_%s", $key, $k));
							$str .= self::var2zval($v, $key2);
							$str .= sprintf("add_index_zval(%s, %d, %s);\n", $key, $k, $key2);
							break;
						case "null":
							$str .= sprintf("add_index_null(%s, %d);\n", $key, $k);
							break;
						}
					} else {
						// assoc string key
						switch ($v_type) {
						case "bool":
						case "double":
						case "long":
							$k = self::escape($k);
							$v = self::escape($v);
							$str .= sprintf("add_assoc_%s(%s, %s, %s);\n", $v_type, $key, $k, $v);
							break;
						case "string":
							$k = self::escape($k);
							$v = self::escape($v);
							$str .= sprintf("add_assoc_string(%s, %s, %s, 0);\n", $key, $k, $v);
							break;
						case "array":
							$key2 = str_replace(" ", "", sprintf("%s_%s", $key, $k));
							$k = self::escape($k);
							$str .= self::var2zval($v, $key2);
							$str .= sprintf("add_assoc_zval(%s, %s, %s);\n", $key, $k, $key2);
							break;
						case "null":
							$k = self::escape($k);
							$str .= sprintf("add_assoc_null(%s, %s);\n", $key, $k);
							break;
						}
					}
				}
			} else {
				// array
				foreach ($value as $k=>$v) {
					$v_type = self::getType($v);
					switch ($v_type) {
					case "bool":
					case "double":
					case "long":
						$v = self::escape($v);
						$str .= sprintf("add_next_index_%s(%s, %s);\n", $v_type, $key, $v);
						break;
					case "string":
						$v = self::escape($v);
						$str .= sprintf("add_next_index_string(%s, %s, 0);\n", $key, $v);
						break;
					case "array":
						$key2 = str_replace(" ", "", sprintf("%s_%s", $key, $k));
						$str .= self::var2zval($v, $key2);
						$str .= sprintf("add_next_index_zval(%s, %s);\n", $key, $key2);
						break;
					case "null":
						$str .= sprintf("add_next_index_null(%s);\n", $key);
						break;
					}
				}
			}
			break;
		}
		return $str;
	}

	public static function getType($value)
	{
		foreach (self::$native_types as $type1=>$type2) {
			$method = "is_".$type1;
			if ($method($value)) {
				return $type2;
			}
		}
		return "null";
	}

	public static function isAssoc($arr)
	{
		return is_array($arr) && array_keys($arr)!==range(0, count($arr)-1);
	}

	public static function escape($v)
	{
		if (is_bool($v)) {
			$v = (int)$v;
		} else if (is_string($v)) {
			$v = '"'.str_replace('"', '\\"', $v).'"';
		} else if (is_null($v)) {
			$v = "NULL";
		}
		return $v;
	}

	public static function indent($str, $n)
	{
		$lines = explode("\n", $str);
		$indent = str_repeat("\t", $n);
		foreach ($lines as &$line) {
			$line = rtrim($indent.$line, "\t");
		}
		return implode("\n", $lines);
	}

	public static function getAccFlag(Reflector $ref)
	{
		if ($ref instanceof ReflectionClass) {
			return self::getClassAccFlag($ref);
		} else if ($ref instanceof ReflectionMethod) {
			return self::getMethodAccFlag($ref);
		} else if ($ref instanceof ReflectionProperty) {
			return self::getPropertyAccFlag($ref);
		}
	}

	public static function getClassAccFlag(ReflectionClass $ref)
	{
		$flag = array();
		if ($ref->isPublic()) {
			$flag[] = "ZEND_ACC_PUBLIC";
		}
		if ($ref->isProtected()) {
			$flag[] = "ZEND_ACC_PROTECTED";
		}
		if ($ref->isPrivate()) {
			$flag[] = "ZEND_ACC_PRIVATE";
		}
		if ($ref->isStatic()) {
			//$flag[] = "ZEND_ACC_STATIC";
			$flag[] = "ZEND_ACC_ALLOW_STATIC";
		}
		if (count($flag)==0) {
			$flag[] = "0";
		}
		return implode(" | ", $flag);
	}

	public static function getMethodAccFlag(ReflectionMethod $ref)
	{
		$flag = array();
		if ($ref->isPublic()) {
			$flag[] = "ZEND_ACC_PUBLIC";
		}
		if ($ref->isProtected()) {
			$flag[] = "ZEND_ACC_PROTECTED";
		}
		if ($ref->isPrivate()) {
			$flag[] = "ZEND_ACC_PRIVATE";
		}
		if ($ref->isStatic()) {
			$flag[] = "ZEND_ACC_STATIC";
			//$flag[] = "ZEND_ACC_ALLOW_STATIC";
		}
		if ($ref->isAbstract()) {
			$flag[] = "ZEND_ACC_ABSTRACT";
		}
		if ($ref->isFinal()) {
			$flag[] = "ZEND_ACC_FINAL";
		}
		if (count($flag)==0) {
			$flag[] = "0";
		}
		return implode(" | ", $flag);
	}

	public static function getPropertyAccFlag(ReflectionProperty $ref)
	{
		$flag = array();
		if ($ref->isPublic()) {
			$flag[] = "ZEND_ACC_PUBLIC";
		}
		if ($ref->isProtected()) {
			$flag[] = "ZEND_ACC_PROTECTED";
		}
		if ($ref->isPrivate()) {
			$flag[] = "ZEND_ACC_PRIVATE";
		}
		if ($ref->isStatic()) {
			//$flag[] = "ZEND_ACC_STATIC";
			$flag[] = "ZEND_ACC_ALLOW_STATIC";
		}
		if (count($flag)==0) {
			$flag[] = "0";
		}
		return implode(" | ", $flag);
	}
}

