<?php

require_once 'PHPH/ReflectionFunction.php';
require_once 'PHPH/Util.php';

class PHPH_ReflectionGlobal
{
	private $_functions = null;
	private $_defines = null;

	public function __construct(array $functions, array $defines)
	{
		$this->_functions = array();
		foreach ($functions as $function) {
			$this->_functions[] = new PHPH_ReflectionFunction($function);
		}
		$this->_defines = $defines;
	}

	public function getFunctions()
	{
		return $this->_functions;
	}

	public function getDefines()
	{
		return $this->_defines;
	}


	// php_xxx.h

	public function getExternFunction()
	{
		$result = "";
		$functions = $this->getFunctions();

		if (0<count($functions)) {
			$result .= "// global function\n";
			foreach ($functions as $function) {
				$fname = $function->getShortName();
				$result .= sprintf("extern PHP_FUNCTION(%s);\n", $fname);
			}
			$result .= "\n";
		}
		return $result;
	}


	// php_xxx.c

	public function getFunctionEntry()
	{
		$result = "";

		$functions = $this->getFunctions();
		foreach ($functions as $function) {
			$ns = $function->getNamespaceName();
			$fname = $function->getShortName();
			$arg_info = sprintf("arginfo_%s", $fname);
			if (isset($ns)) {
				$ns = PHPH_Util::escape($ns);
				$result .= sprintf("ZEND_NS_FE(%s, %s, %s)\n", $ns, $fname, $arg_info);
			} else {
				$result .= sprintf("PHP_FE(%s, %s)\n", $fname, $arg_info);
			}
		}
		return PHPH_Util::indent($result, 1);
	}

	public function getArgInfo()
	{
		$functions = $this->getFunctions();
		
		$result = "// global function arginfo\n";
		foreach ($functions as $function) {
			$result .= $function->getArgInfo()."\n";
		}
		return $result;
	}

	public function getModuleInit()
	{
		$result = "";

		$defines = $this->getDefines();
		if (0<count($defines)) {
			$result .= "// global const\n";
			foreach ($defines as $key=>$value) {
				$ns = null;
				if (preg_match('/^(.+)\\\\([^\\\\]+)$/', $key, $match)) {
					$ns = $match[1];
					$key = $match[2];
				}
				$result .= PHPH_Util::getZendRegisterConstant($ns, $key, $value);
			}
			$result .= "\n";
		}
		return PHPH_Util::indent($result, 1);
	}


	// xxx.c

	public function getPHPFunction()
	{
		$functions = $this->getFunctions();

		$result = "";
		if (0<count($functions)) {
			$result = "/* global function\n */\n";
			foreach ($functions as $function) {
				$result .= $function->getPHPFunction()."\n";
			}
			$result .= "\n";
		}
		return $result;
	}
}
