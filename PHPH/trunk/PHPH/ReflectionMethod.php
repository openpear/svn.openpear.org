<?php

require_once 'PHPH.php';
require_once 'PHPH/ReflectionParameter.php';
require_once 'PHPH/Util.php';
require_once 'PHPH/Exception.php';

class PHPH_ReflectionMethod extends ReflectionMethod
{
	public function getParameters()
	{
		$params = parent::getParameters();
		$result = array();
		$function = array($this->class, $this->getName());
		foreach ($params as $param) {
			$result[] = new PHPH_ReflectionParameter($function, $param->getName());
		}
		return $result;
	}


	// php_xxx.c

	public function getArgInfo()
	{
		$class_lower = strtolower($this->class);
		$params = $this->getParameters();

		$argname = sprintf("arginfo_%s_%s", $class_lower, $this->getName());
		$required_count = $this->getNumberOfRequiredParameters();

		$result = sprintf("ZEND_BEGIN_ARG_INFO_EX(%s, 0, 0, %d)\n", $argname, $required_count);
		foreach ($params as $param) {
			$class = $param->getClass();
			$is_ref = $param->isPassedByReference();
			$param_name = $param->getName();
			if ($class) {
				$class_name = $class->getName();
				$result .= sprintf("\tZEND_ARG_OBJ_INFO(%d, %s, %s, 0)\n", $is_ref, $param_name, $class_name);
			} else if ($param->isArray()) {
				$result .= sprintf("\tZEND_ARG_ARRAY_INFO(%d, %s, 0)\n", $is_ref, $param_name);
			} else {
				$result .= sprintf("\tZEND_ARG_INFO(%d, %s)\n", $is_ref, $param_name);
			}
		}
		$result .= "ZEND_END_ARG_INFO()\n";
		return $result;
	}


	// xxx.c

	public function getPrototype()
	{
		$class = $this->class;
		$operator = $this->isStatic() ? "::" : "->";
		$method = $this->getName();

		$params = $this->getParameters();
		$args = array();
		foreach ($params as $param) {
			$arg = array();
			$arg_class = $param->getClass();
			if ($arg_class) {
				$arg[] = $arg_class->getName();
			}
			$arg[] = sprintf('$%s', $param->getName());
			if ($param->isOptional()) {
				try {
					$default_value = $param->getDefaultValue();
					$arg[] = "=";
					$arg[] = $default_value;
				} catch (ReflectionException $e) {
				}
			}
			$args[] = implode(" ", $arg);
		}
		$arg_str = implode(", ", $args);
		return sprintf("%s%s%s(%s)", $class, $operator, $method, $arg_str);
	}

	public function getPHPMethod()
	{
		$class_lower = strtolower($this->class);

		$result = sprintf("// %s;\n", $this->getPrototype());
		$result .= sprintf("PHP_METHOD(%s, %s)\n{\n", $class_lower, $this->getName());
		if (PHPH::getInstance()->getClass($this->class) && !$this->isAbstract()) {
			$body = "";
			$efree = "";
			$params = $this->getParameters();
			if (0<count($params)) {
				$is_required = true;
				$type_spec = "";
				$args = array();
				$declare = array();
				foreach ($params as $param) {
					if ($is_required && $param->isOptional()) {
						$type_spec .= "|";
						$is_required = false;
					}
					$type_spec .= $param->getTypeSpec();
					$args[] = $param->getArgument();
					$declare = array_merge($declare, $param->getDeclare());
					$efree .= $param->getEfree();
				}
				$declare = implode("\n", array_unique($declare))."\n\n";
				$args = implode(", ", $args);
				$body .= $declare;
				$body .= sprintf("if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, \"%s\", %s) == FAILURE) {\n", $type_spec, $args);
				$body .= "\treturn;\n";
				$body .= "}\n";
			} else {
				$body .= "if (zend_parse_parameters_none() == FAILURE) {\n";
				$body .= "\treturn;\n";
				$body .= "}\n";
			}
			$body .= "\n";
			$body .= "// ...\n";
			if (0<strlen($efree)) {
				$body .= "\n".$efree;
			}
			// override
			if ($this->hasParentMethod()) {
				$class = new PHPH_ReflectionClass($this->class);
				$parent = strtolower($class->getParentName());
				$pbody = "/*\n";
				$pbody .= sprintf("PHP_METHOD_PASSTHRU(%s, %s)\n", $parent, $this->getName());
				$pbody .= "return;\n";
				$pbody .= "/*\n";
				$body = $pbody.$body;
			}
			$result .= PHPH_Util::indent($body, 1);
		}
		$result .= "}\n";
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
			$flag[] = "ZEND_ACC_STATIC";
			//$flag[] = "ZEND_ACC_ALLOW_STATIC";
		}
		if ($this->isAbstract()) {
			$flag[] = "ZEND_ACC_ABSTRACT";
		}
		if ($this->isFinal()) {
			$flag[] = "ZEND_ACC_FINAL";
		}
		if (count($flag)==0) {
			$flag[] = "0";
		}
		return implode(" | ", $flag);
	}

	public function hasParentMethod()
	{
		$class = new PHPH_ReflectionClass($this->class);
		$parent = $class->getParentClass();
		if (isset($parent)) {
			return $parent->hasMethod($this->getName());
		}
		return false;
	}
}
