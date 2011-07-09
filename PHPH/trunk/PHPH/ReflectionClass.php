<?php

require_once 'PHPH/ReflectionMethod.php';
require_once 'PHPH/TopologicalSort.php';
require_once 'PHPH/Util.php';

class PHPH_ReflectionClass extends ReflectionClass
{
	public function getNamespaceName()
	{
		$ns = null;
		if (method_exists("ReflectionClass", "getNamespaceName")) {
			$ns_tmp = parent::getNamespaceName();
			if (0<strlen($ns_tmp)) {
				$ns = $ns_tmp;
			}
		}
		return $ns;
	}

	public function getShortName()
	{
		$name = null;
		if (method_exists("ReflectionClass", "getShortName")) {
			$name = parent::getShortName();
		} else {
			$name = $this->getName();
		}
		return $name;
	}

	public function getParentClass()
	{
		$parent = parent::getParentClass();
		if ($parent!==false) {
			return $parent;
		}
		return null;
	}

	public function getParentName()
	{
		$parent = $this->getParentClass();
		if (isset($parent)) {
			return $parent->getName();
		}
		return null;
	}

	public function getMethod($name)
	{
		$method = parent::getMethod($name);
		if ($method) {
			return new PHPH_ReflectionMethod($this->getName(), $method->getName());
		}
		return null;
	}

	public function getMethods()
	{
		$methods = parent::getMethods();
		$result = array();
		foreach ($methods as $method) {
			$result[] = new PHPH_ReflectionMethod($this->getName(), $method->getName());
		}
		return $result;
	}


	// build php_xxx.h

	public function getExternClassEntry()
	{
		return "extern ".$this->getClassEntry();
	}

	public function getExternMethod()
	{
		$class = $this->getName();
		$class_lower = strtolower(str_replace("\\", "_", $class));
		$methods = $this->getMethods();

		$result = "";
		if (0<count($methods)) {
			$result = sprintf("// %s method\n", $class);
			foreach ($methods as $method) {
				$result .= sprintf("extern PHP_METHOD(%s, %s);\n", $class_lower, $method->name);
			}
			$result .= "\n";
		}
		return $result;
	}


	// build php_xxx.c

	public function getClassEntry()
	{
		$class = $this->getName();
		$class = strtolower(str_replace("\\", "_", $class));
		return sprintf("PHPAPI zend_class_entry *ce_%s;\n", $class);
	}

	public function getArgInfo()
	{
		$class = $this->getName();
		$class_lower = strtolower(str_replace("\\", "_", $class));
		$methods = $this->getMethods();

		$result = "";
		if (0<count($methods)) {
			$result = sprintf("// %s arginfo\n", $class);
			foreach ($methods as $method) {
				$result .= $method->getArgInfo()."\n";
			}
		}
		return $result;
	}

	public function getMethodEntry()
	{
		$class = $this->getName();
		$class_lower = strtolower(str_replace("\\", "_", $class));
		$methods = $this->getMethods();

		$result = sprintf("// %s method\n", $class);
		$result .= sprintf("zend_function_entry %s_methods[] = {\n", $class_lower);
		foreach ($methods as $method) {
			$flag = PHPH_Util::getAccFlag($method);
			$result .= sprintf("\tPHP_ME(%s, %s, arginfo_%s_%s, %s)\n", $class_lower, $method->name, $class_lower, $method->name, $flag);
		}
		$result .= "\t{ NULL, NULL, NULL }\n";
		$result .= "};\n\n";
		return $result;
	}

	public function getModuleInit()
	{
		$class = $this->getName();
		$class_lower = strtolower(str_replace("\\", "_", $class));
		$ns = $this->getNamespaceName();

		$consts = $this->getConstants();
		$properties = $this->getProperties();

		// init
		$result = sprintf("// %s\n", $class);
		if (isset($ns)) {
			$ns = PHPH_Util::escape($ns);
			$esc_class = PHPH_Util::escape($this->getShortName());
			$result .= sprintf("INIT_NS_CLASS_ENTRY(ce, %s, %s, %s_methods);\n", $ns, $esc_class, $class_lower);
		} else {
			$esc_class = PHPH_Util::escape($class);
			$result .= sprintf("INIT_CLASS_ENTRY(ce, %s, %s_methods);\n", $esc_class, $class_lower);
		}

		if (!$this->isInterface()) {
			// class
			$gen = PHPH_Generator::getInstance();
			// regist
			$parent = $this->getParentClass();
			if (isset($parent)) {
				// extends
				// todo php5.3 namespace
				if ($gen->getClass($parent->getName())) {
					// parent class is managed
					$pclass_lower = strtolower($parent->getName());
					$result .= sprintf("ce_%s = zend_register_internal_class_ex(&ce, ce_%s, NULL TSRMLS_CC);\n", $class_lower, $pclass_lower);
				} else {
					// parent class is not managed
					$esc_parent = PHPH_Util::escape($parent->getName());
					$result .= sprintf("pce = zend_fetch_class(%s, sizeof(%s)-1, ZEND_FETCH_CLASS_AUTO TSRMLS_CC);\n", $esc_parent, $esc_parent);
					$result .= "if (pce) {\n";
					$result .= sprintf("\tce_%s = zend_register_internal_class_ex(&ce, pce, NULL TSRMLS_CC);\n", $class_lower);
					$result .= "} else {\n";
					$result .= sprintf("\tce_%s = zend_register_internal_class(&ce TSRMLS_CC);\n", $class_lower);
					$result .= "}\n";
				}
			} else {
				// not extends
				$result .= sprintf("ce_%s = zend_register_internal_class(&ce TSRMLS_CC);\n", $class_lower);
			}

			// interface
			$interfaces = $this->getInterfaceNames();
			foreach ($interfaces as $interface) {
				// todo php5.3 namespace
				if ($gen->getInterface($interface)) {
					$result .= sprintf("ice = ce_%s;\n", strtolower($interface));
				} else {
					$esc_interface = PHPH_Util::escape($interface);
					$result .= sprintf("ice = zend_fetch_class(%s, sizeof(%s)-1, ZEND_FETCH_CLASS_AUTO TSRMLS_CC);\n", $esc_interface, $esc_interface);
				}
				$result .= "if (ice) {\n";
				$result .= sprintf("\tphph_register_implement(ce_%s, ice TSRMLS_CC);\n", $class_lower);
				$result .= "}\n";
			}
			$result .= "\n";
		} else {
			// interface
			// regist
			// todo php5.3 namespace
			$result .= sprintf("ce_%s = zend_register_internal_interface(&ce TSRMLS_CC);\n", $class_lower);
			$result .= "\n";
		}

		// const
		if (0<count($consts)) {
			$result .= sprintf("// %s const\n", $class);
			foreach ($consts as $key=>$value) {
				$result .= PHPH_Util::getZendDeclareConst($class_lower, $key, $value);
			}
			$result .= "\n";
		}

		// property
		if (0<count($properties)) {
			$values = get_class_vars($class);
			$result .= sprintf("// %s property\n", $class);
			foreach ($properties as $property) {
				$key = $property->name;
				$value = $values[$key];
				$flag = PHPH_Util::getAccFlag($property);
				$result .= PHPH_Util::getZendDeclareProperty($class_lower, $key, $value, $flag);
			}
			$result .= "\n";
		}

		return PHPH_Util::indent($result, 1);
	}


	// xxx.c

	public function getPHPMethod()
	{
		$class = $this->getName();
		$class_lower = strtolower(str_replace("\\", "_", $class));
		$methods = $this->getMethods();

		$result = sprintf("/* %s method\n */\n", $class);
		foreach ($methods as $method) {
			$result .= $method->getPHPMethod()."\n";
		}
		$result .= "\n";
		return $result;
	}


	// static

	public static function sortClasses($classes)
	{
		$hash = array();
		$ts = new PHPH_TopologicalSort();

		foreach ($classes as $class) {
			$hash[$class->getName()] = $class;
			$ts->addNode($class->getParentName(), $class->getName());
		}
		$sorted = $ts->sort();

		$result = array();
		foreach ($sorted as $name) {
			if (isset($hash[$name])) {
				$result[] = $hash[$name];
			}
		}
		return $result;
	}
}
