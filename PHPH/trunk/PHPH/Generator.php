<?php

require_once 'PHPH/Skeleton.php';
require_once 'PHPH/ReflectionClass.php';
require_once 'PHPH/ReflectionGlobal.php';
require_once 'PHPH/ReflectionFunction.php';

// PHP Extension source generator
class PHPH_Generator
{
	private static $_instance = null;

	private $_extname_upper = null;
	private $_extname_lower = null;

	private $_before_classes = null;
	private $_before_interfaces = null;
	private $_before_functions = null;
	private $_before_defines = null;

	public $classes = null;
	public $interfaces = null;
	public $global = null;


	private function __construct($extname)
	{
		// check extname
		if (!preg_match('/^[a-zA-Z0-9]+$/', $extname)) {
			throw new Exception("extname is not valid");
		}
		$this->_extname_upper = strtoupper($extname);
		$this->_extname_lower = strtolower($extname);

		// set before
		$this->_before_classes = get_declared_classes();
		$this->_before_interfaces = get_declared_interfaces();
		$functions = get_defined_functions();
		$this->_before_functions = $functions["user"];
		$this->_before_defines = get_defined_constants();
	}

	public static function getInstance($extname=null)
	{
		if (is_null(self::$_instance) && isset($extname)) {
			self::$_instance = new PHPH_Generator($extname);
		}
		return self::$_instance;
	}


	// differentia of declaration

	public function includeFile($path)
	{
		// realpath
		$realpath = realpath($path);

		// include
		if (!is_file($realpath)) {
			throw new Exception("file is not found: ".$path);
		}
		include_once $realpath;
	}

	public function includeFiles($paths)
	{
		foreach ($paths as $path) {
			$this->includeFile($path);
		}
	}

	public function finish()
	{
		$classes = get_declared_classes();
		$interfaces = get_declared_interfaces();
		$functions = get_defined_functions();
		$functions = $functions["user"];
		$defines = get_defined_constants();

		// class
		$this->classes = array();
		$classes = array_diff($classes, $this->_before_classes);
		foreach ($classes as $class) {
			$this->classes[] = new PHPH_ReflectionClass($class);
		}
		$this->classes = PHPH_ReflectionClass::sortClasses($this->classes);

		// interface
		$this->interfaces = array();
		$interfaces = array_diff($interfaces, $this->_before_interfaces);
		foreach ($interfaces as $interface) {
			$this->interfaces[] = new PHPH_ReflectionClass($interface);
		}
		$this->interfaces = PHPH_ReflectionClass::sortClasses($this->interfaces);

		// global
		$functions = array_diff($functions, $this->_before_functions);
		$defines = array_diff($defines, $this->_before_defines);
		$this->global = new PHPH_ReflectionGlobal($functions, $defines);
	}

	public function getClass($class_name)
	{
		foreach ($this->classes as $class) {
			if ($class->getName()==$class_name) {
				return $class;
			}
		}
		return null;
	}

	public function getInterface($interface_name)
	{
		foreach ($this->interfaces as $interface) {
			if ($interface->getName()==$interface_name) {
				return $interface;
			}
		}
		return null;
	}

	public function getFunction($function_name)
	{
		foreach ($this->global->getFunctions() as $function) {
			if ($function->getName()==$function_name) {
				return $function;
			}
		}
		return null;
	}


	// generator

	public function generatePHPH()
	{
		$data = array(
			"extname" => $this->_extname_lower,
			"prototype" => "prototype_files",
			"configure" => ""
		);
		$ini = "";
		foreach ($data as $k=>$v) {
			$ini .= sprintf("%s=%s\n", $k, $v);
		}
		return $ini;
	}

	public function generateConfigM4()
	{
		$txt = PHPH_Skeleton::loadConfigM4();
		$txt = str_replace("extname", $this->_extname_lower, $txt);
		$txt = str_replace("EXTNAME", $this->_extname_upper, $txt);
		return $txt;
	}

	public function generateConfigW32()
	{
		$txt = PHPH_Skeleton::loadConfigW32();
		$txt = str_replace("extname", $this->_extname_lower, $txt);
		$txt = str_replace("EXTNAME", $this->_extname_upper, $txt);
		return $txt;
	}

	public function generateH()
	{
		$head = "";

		// class entry
		if (0<count($this->classes)) {
			$head .= "// class entry\n";
			foreach ($this->classes as $class) {
				$head .= $class->getExternClassEntry();
			}
			$head .= "\n";
		}

		// interface method
		if (0<count($this->interfaces)) {
			foreach ($this->interfaces as $interface) {
				$head .= $interface->getExternMethod();
			}
		}

		// class method
		if (0<count($this->classes)) {
			foreach ($this->classes as $class) {
				$head .= $class->getExternMethod();
			}
		}

		// global function
		$head .= $this->global->getExternFunction();

		$txt = PHPH_Skeleton::loadH();
		$txt = str_replace("extname", $this->_extname_lower, $txt);
		$txt = str_replace("EXTNAME", $this->_extname_upper, $txt);
		$txt = str_replace("%%PHPH_HEADER%%\n", $head, $txt);
		return $txt;
	}

	public function generateC()
	{
		$class_entry = "";
		$arg_info = "";
		$function_entry = "";
		$method_entry = "";
		$module_init = "";

		// global
		$arg_info .= $this->global->getArgInfo();
		$function_entry = $this->global->getFunctionEntry();
		$module_init .= $this->global->getModuleInit();

		// interface
		foreach ($this->interfaces as $interface) {
			$class_entry .= $interface->getClassEntry();
			$arg_info .= $interface->getArgInfo();
			$method_entry .= $interface->getMethodEntry();
			$module_init .= $interface->getModuleInit();
		}

		// class
		foreach ($this->classes as $class) {
			$class_entry .= $class->getClassEntry();
			$arg_info .= $class->getArgInfo();
			$method_entry .= $class->getMethodEntry();
			$module_init .= $class->getModuleInit();
		}

		$txt = PHPH_Skeleton::loadC();
		$txt = str_replace("extname", $this->_extname_lower, $txt);
		$txt = str_replace("EXTNAME", $this->_extname_upper, $txt);
		$txt = str_replace("%%PHPH_CLASS_ENTRY%%\n", $class_entry, $txt);
		$txt = str_replace("%%PHPH_ARG_INFO%%\n", $arg_info, $txt);
		$txt = str_replace("%%PHPH_FUNCTION_ENTRY%%\n", $function_entry, $txt);
		$txt = str_replace("%%PHPH_METHOD_ENTRY%%\n", $method_entry, $txt);
		$txt = str_replace("%%PHPH_MINIT%%\n", $module_init, $txt);
		return $txt;
	}

	public function generateMain()
	{
		$php_function = "";

		// interface
		foreach ($this->interfaces as $interface) {
			$php_function .= $interface->getPHPMethod();
		}

		// class
		foreach ($this->classes as $class) {
			$php_function .= $class->getPHPMethod();
		}

		// global
		$php_function .= $this->global->getPHPFunction();

		$txt = PHPH_Skeleton::loadMain();
		$txt = str_replace("extname", $this->_extname_lower, $txt);
		$txt = str_replace("EXTNAME", $this->_extname_upper, $txt);
		$txt = str_replace("%%PHPH_PHP_FUNCTION%%\n", $php_function, $txt);
		return $txt;
	}

	public function generateDSP()
	{
		$txt = PHPH_Skeleton::loadDSP();
		$txt = str_replace("extname", $this->_extname_lower, $txt);
		$txt = str_replace("EXTNAME", $this->_extname_upper, $txt);
		return $txt;
	}

	public function generatePHP()
	{
		$txt = PHPH_Skeleton::loadPHP();
		$txt = str_replace("extname", $this->_extname_lower, $txt);
		$txt = str_replace("EXTNAME", $this->_extname_upper, $txt);
		return $txt;
	}

	public function generatePHPT()
	{
		$txt = PHPH_Skeleton::loadPHPT();
		$txt = str_replace("extname", $this->_extname_lower, $txt);
		$txt = str_replace("EXTNAME", $this->_extname_upper, $txt);
		return $txt;
	}

	public function generateDefineTest()
	{
		$check = "";

		// interface
		foreach ($this->interfaces as $interface) {
			$check .= sprintf("check('interface', '%s');\n", $interface->getName());
		}

		// class
		foreach ($this->classes as $class) {
			$check .= sprintf("check('class', '%s');\n", $class->getName());
		}

		// global
		foreach ($this->global->getFunctions() as $function) {
			$check .= sprintf("check('function', '%s');\n", $function->getName());
		}

		$txt = PHPH_Skeleton::loadDefineTest();
		$txt = str_replace("extname", $this->_extname_lower, $txt);
		$txt = str_replace("EXTNAME", $this->_extname_upper, $txt);
		$txt = str_replace("%%CHECK%%", $check, $txt);
		return $txt;
	}
}
