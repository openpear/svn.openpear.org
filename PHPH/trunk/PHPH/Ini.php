<?php

class PHPH_Ini
{
	private static $_instance = null;

	private $_ini = null;

	private function __construct()
	{
		$this->_ini = @parse_ini_file(".phph");
		if (!$this->_ini || !isset($this->_ini["extname"])) {
			throw new Exception("Current directory is not phph project directory");
		}
	}

	public static function getInstance()
	{
		if (!self::$_instance) {
			self::$_instance = new PHPH_Ini();
		}
		return self::$_instance;
	}

	public static function get($key, $default_value=null)
	{
		$ini = self::getInstance();
		if (isset($ini->_ini[$key])) {
			return $ini->_ini[$key];
		}
		return $default_value;
	}
}
