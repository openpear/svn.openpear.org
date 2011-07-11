<?php

require_once 'PHPH.php';
require_once 'PHPH/Util.php';

class PHPH_ReflectionParameter extends ReflectionParameter
{
	const TYPE_LONG						= "l";
	const TYPE_DOUBLE					= "d";
	const TYPE_STRING					= "s";
	const TYPE_STRICT_STRING			= "S";
	const TYPE_UNICODE					= "u";
	const TYPE_STRICT_UNICODE			= "U";
	const TYPE_SEMANTICS_STRING			= "t";
	const TYPE_CONVERSION_STRING		= "T";
	const TYPE_BOOLEAN					= "b";
	const TYPE_RESOURCE					= "r";
	const TYPE_ARRAY					= "a";
	const TYPE_HASH						= "h";
	const TYPE_OBJECT					= "o";
	const TYPE_CLASS_SPECIFIED_OBJECT	= "O";
	const TYPE_CLASS_NAME				= "C";
	const TYPE_CALLBACK					= "f";
	const TYPE_ZVAL						= "z";
	const TYPE_ZVAL_PTR					= "Z";
	const TYPE_OPTIONAL_VARS			= "*";	// v
	const TYPE_REQUIRED_VARS			= "+";	// V

	public function getName()
	{
		return preg_replace("/^[ldsSuUtTbrahoOCfzZvV]_/", "", parent::getName());
	}

	public function getTypeSpec()
	{
		$code = self::TYPE_ZVAL;
		if (preg_match("/^([ldsSuUtTbrahoOCfzZvV])_/", parent::getName(), $m)) {
			$code = $m[1];
			switch ($code) {
			case "v":
				$code = self::TYPE_OPTIONAL_VARS;
				break;
			case "V":
				$code = self::TYPE_REQUIRED_VARS;
				break;
			}
		}

		$type = self::TYPE_ZVAL;
		if ($this->getClass()) {
			// class specified
			$type = self::TYPE_CLASS_SPECIFIED_OBJECT;
		} else if ($this->isArray()) {
			// array
			if ($code==self::TYPE_HASH) {
				// hash
				$type = self::TYPE_HASH;
			} else {
				// array
				$type = self::TYPE_ARRAY;
			}
		} else if ($this->isPassedByReference()) {
			// reference, must zval
			$type = self::TYPE_ZVAL;
		} else {
			$type = $code;
		}
		return $type;
	}

	public function getDeclare()
	{
		$name = $this->getName();
		$result = array();

		switch ($this->getTypeSpec()) {
		case self::TYPE_LONG:
			$result[] = sprintf("int %s;", $name);
			break;
		case self::TYPE_DOUBLE:
			$result[] = sprintf("double %s;", $name);
			break;
		case self::TYPE_STRING:
		case self::TYPE_STRICT_STRING:
			$result[] = sprintf("char *%s;", $name);
			$result[] = sprintf("int %s_len;", $name);
			break;
		case self::TYPE_UNICODE:
		case self::TYPE_STRICT_UNICODE:
			$result[] = sprintf("UChar *%s;", $name);
			$result[] = sprintf("int %s_len;", $name);
			break;
		case self::TYPE_SEMANTICS_STRING:
		case self::TYPE_CONVERSION_STRING:
			$result[] = sprintf("zstr *%s;", $name);
			$result[] = sprintf("int %s_len;", $name);
			$result[] = sprintf("zend_uchar %s_type;", $name);
			break;
		case self::TYPE_BOOLEAN:
			$result[] = sprintf("zend_bool %s", $name);
			break;
		case self::TYPE_RESOURCE:
		case self::TYPE_ARRAY:
		case self::TYPE_OBJECT:
		case self::TYPE_ZVAL:
			$result[] = sprintf("zval *%s;", $name);
			break;
		case self::TYPE_HASH:
			$result[] = sprintf("HashTable *%s;", $name);
			break;
		case self::TYPE_CLASS_SPECIFIED_OBJECT:
			$class = $this->getClass()->getName();
			$result[] = sprintf("zval *%s;", $name);
			$phph = PHPH::getInstance();
			if (!$phph->getClass($class) && !$phph->getInterface($class)) {
				$class_lower = strtolower($class);
				$esc_class = PHPH_Util::escape($class);
				$result[] = sprintf("zend_class_entry *ce_%s = zend_fetch_class(%s, sizeof(%s)-1, ZEND_FETCH_CLASS_AUTO TSRMLS_CC);", $class_lower, $esc_class, $esc_class);
			}
			break;
		case self::TYPE_CLASS_NAME:
			$result[] = sprintf("zend_class_entry *%s;", $name);
			break;
		case self::TYPE_CALLBACK:
			$result[] = sprintf("zend_fcall_info %s;", $name);
			$result[] = sprintf("zend_fcall_info_cache %s_cache;", $name);
			break;
		case self::TYPE_ZVAL_PTR:
			$result[] = sprintf("zval **%s;", $name);
			break;
		case self::TYPE_OPTIONAL_VARS:
		case self::TYPE_REQUIRED_VARS:
			$result[] = sprintf("zval ***%s;", $name);
			$result[] = sprintf("int %s_num;", $name);
			break;
		}
		return $result;
	}

	public function getArgument()
	{
		$name = $this->getName();
		$result = "";

		switch ($this->getTypeSpec()) {
		case self::TYPE_LONG:
		case self::TYPE_DOUBLE:
		case self::TYPE_BOOLEAN:
		case self::TYPE_RESOURCE:
		case self::TYPE_ARRAY:
		case self::TYPE_OBJECT:
		case self::TYPE_ZVAL:
		case self::TYPE_HASH:
		case self::TYPE_CLASS_NAME:
		case self::TYPE_ZVAL_PTR:
			$result = sprintf("&%s", $name);
			break;
		case self::TYPE_STRING:
		case self::TYPE_STRICT_STRING:
		case self::TYPE_UNICODE:
		case self::TYPE_STRICT_UNICODE:
			$result = sprintf("&%s, &%s_len", $name, $name);
			break;
		case self::TYPE_SEMANTICS_STRING:
		case self::TYPE_CONVERSION_STRING:
			$result = sprintf("&%s, &%s_len, %s_type", $name, $name, $name);
			break;
		case self::TYPE_CLASS_SPECIFIED_OBJECT:
			$class_lower = strtolower($this->getClass()->getName());
			$result = sprintf("&%s, ce_%s", $name, $class_lower);
			break;
		case self::TYPE_CALLBACK:
			$result = sprintf("&%s, &%s_cache", $name, $name);
			break;
		case self::TYPE_OPTIONAL_VARS:
		case self::TYPE_REQUIRED_VARS:
			$result = sprintf("&%s, &%s_num", $name, $name);
			break;
		}
		return $result;
	}

	public function getAccessFlag()
	{
		$flag = array();
		if ($this->isPublic()) {
			$flag[] = "ZEND_ACC_PUBLIC";
		}
		if ($this->isProtected()) {
			$flag[] = "ZEND_ACC_PROTECTED";
		}
		if ($this->isPrivate()) {
			$flag[] = "ZEND_ACC_PRIVATE";
		}
		if ($this->isStatic()) {
			//$flag[] = "ZEND_ACC_STATIC";
			$flag[] = "ZEND_ACC_ALLOW_STATIC";
		}
		if (count($flag)==0) {
			$flag[] = "0";
		}
		return implode(" | ", $flag);
	}
}
