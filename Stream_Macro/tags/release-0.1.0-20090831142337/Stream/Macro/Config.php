<?php
class Stream_Macro_Config
{
	private $_res;
	
	private function __construct(){}
	
	static public function getConfigByArray($config)
	{
		$inct = new self();
		$inct->_res = $config;
		return array($inct, 'macroConfigByArray');
	}
	
	static public function getConfigByINI($filepath)
	{
		$inct = new self();
		$inct->_res = parse_ini_file($filepath);
		return array($inct, 'macroConfigByArray');
	}
	
	public function macroConfigByArray($path)
	{
		return $this->_res;
	}
}
