<?php

require_once 'PHPH/Generator.php';
require_once 'Console/CommandLine.php';

class PHPH
{
	private $_parser = null;

	public function __construct()
	{
		// parser
		$parser = new Console_CommandLine(
			array(
				'description' => 'PHP Extension source generator',
				'version'     => '1.0.0'
			)
		);

		// subcommand new
		$init_cmd = $parser->addCommand(
			'init',
			array(
				'description' => 'create new extension source',
				'aliases' => array('new', 'create', 'generate')
			)
		);

		$init_cmd->addArgument(
			'extname',
			array(
				'description' => 'additional files'
			)
		);

		$init_cmd->addArgument(
			'prototype_files',
			array(
				'multiple'    => true,
				'description' => 'additional prototype files',
				'optional'    => true
			)
		);

		// subcommand update
		$update_cmd = $parser->addCommand(
			'update',
			array(
				'description' => 'update extension source',
				'aliases' => array('up')
			)
		);

		$update_cmd->addOption(
			'yes',
			array(
				'short_name'  => '-y',
				'long_name'   => '--yes',
				'action'      => 'StoreTrue',
				'description' => 'overwrite'
			)
		);

		$update_cmd->addOption(
			'no',
			array(
				'short_name'  => '-n',
				'long_name'   => '--no',
				'action'      => 'StoreTrue',
				'description' => 'non overwrite'
			)
		);

		// subcommand make
		$make_cmd = $parser->addCommand(
			'make',
			array(
				'description' => 'compile extension'
			)
		);

		// subcommand make install
		$make_install_cmd = $make_cmd->addCommand(
			'install',
			array(
				'description' => 'install extension'
			)
		);

		// subcommand template
		$template_cmd = $parser->addCommand(
			'template',
			array(
				'description' => 'show function template'
			)
		);

		// subcommand template method
		$template_cmd->addCommand(
			'method_name',
			array(
				'description' => 'target method name'
			)
		);

		$this->_parser = $parser;
	}

	public function start()
	{
		$parser = $this->_parser;
		try {
			$arg = $parser->parse();
			// no command
			if (!$arg->command_name) {
				throw Console_CommandLine_Exception::factory(
					'INVALID_SUBCOMMAND',
					array('command' => ""),
					$parser,
					$parser->messages
				);
			}
			// sub command
			$method = "_do".ucfirst($arg->command_name);
			$this->$method($arg);
		} catch (Console_CommandLine_Exception $e) {
			$parser->displayError($e->getMessage(), false);
			$parser->displayUsage();
		} catch (Exception $e) {
			echo $e->getMessage()."\n";
		}
	}

	private function _doInit(Console_CommandLine_Result $arg)
	{
		// arg
		$extname = $arg->command->args["extname"];
		$prototype_files = $arg->command->args["prototype_files"];

		// check dir
		if (is_dir($extname)) {
			throw new Exception("ext already exists: ".$extname);
		}

		// init generator
		$gen = PHPH_Generator::getInstance($extname);
		$gen->includeFiles($prototype_files);
		$gen->finish();

		// create file
		self::createDir($extname);
		self::putFile(array($extname, '.phph'), $gen->generatePHPH());
		self::putFile(array($extname, 'config.m4'), $gen->generateConfigM4());
		self::putFile(array($extname, 'config.w32'), $gen->generateConfigW32());
		self::putFile(array($extname, 'php_'.$extname.'.h'), $gen->generateH());
		self::putFile(array($extname, 'php_'.$extname.'.c'), $gen->generateC());
		self::putFile(array($extname, $extname.'.c'), $gen->generateMain());
		self::putFile(array($extname, $extname.'.dsp'), $gen->generateDSP());
		self::putFile(array($extname, $extname.'.php'), $gen->generatePHP());
		self::createDir(array($extname, 'tests'));
		self::putFile(array($extname, 'tests', '001.phpt'), $gen->generatePHPT());
		self::putFile(array($extname, 'definetest.php'), $gen->generateDefineTest());
		self::createDir(array($extname, 'prototype_files'));
		foreach ($prototype_files as $prototype_file) {
			self::copyFile($prototype_file, array($extname, 'prototype_files'));
		}
	}

	private function _doUpdate(Console_CommandLine_Result $arg)
	{
		// check 
		$ini = @parse_ini_file(".phph");
		if (!$ini || !$ini["extname"]) {
			throw new Exception("Current directory is not phph directory");
		}

		// arg
		$extname = @$ini["extname"];
		$prototype = @$ini["prototype"];
		$overwrite = null;
		if (isset($arg->command->options["yes"])) {
			$overwrite = true;
		}
		if (isset($arg->command->options["no"])) {
			$overwrite = false;
		}

		// prototype files
		if (!is_dir($prototype)) {
			throw new Exception("protytype directory is not found: ".$prototype);
		}
		ini_set('include_path', $prototype.PATH_SEPARATOR.ini_get('include_path'));
		$prototype_files = self::fileList($prototype);

		// init generator
		$gen = PHPH_Generator::getInstance($extname);
		$gen->includeFiles($prototype_files);
		$gen->finish();

		// update file
		self::putFile('php_'.$extname.'.h', $gen->generateH());
		self::putFile('php_'.$extname.'.c', $gen->generateC());
		self::putFile($extname.'.c', $gen->generateMain(), $overwrite);
		self::putFile('definetest.php', $gen->generateDefineTest());
	}

	private function _doMake(Console_CommandLine_Result $arg)
	{
		// arg
		$install = $arg->command->command_name=="install";

		// check 
		$ini = @parse_ini_file(".phph");
		if (!$ini || !@$ini["extname"]) {
			throw new Exception("Current directory is not phph directory");
		}

		// phpize configure make
		$configure = self::normalizePath(".", "configure ").@$ini["configure"];
		$res = self::cmd("phpize") && self::cmd($configure) && self::cmd("make");

		// install
		if ($install && $res) {
			self::cmd("make install");
		}
	}

	private function _doTemplate(Console_CommandLine_Result $arg)
	{
		// check 
		$ini = @parse_ini_file(".phph");
		if (!$ini || !$ini["extname"]) {
			throw new Exception("Current directory is not phph directory");
		}
		$extname = @$ini["extname"];
		$prototype = @$ini["prototype"];

		// prototype files
		if (!is_dir($prototype)) {
			throw new Exception("protytype directory is not found: ".$prototype);
		}
		ini_set('include_path', $prototype.PATH_SEPARATOR.ini_get('include_path'));
		$prototype_files = self::fileList($prototype);

		// init generator
		$gen = PHPH_Generator::getInstance($extname);
		$gen->includeFiles($prototype_files);
		$gen->finish();

		// template
		$method_name = $arg->command->command_name;
		if ($method_name!==false) {
			$class_name = null;
			if (strpos($method_name, "::")) {
				// method
				list($class_name, $method_name) = explode("::", $method_name, 2);
				$class = null;
				if (!$class) $class = $gen->getClass($class_name);
				if (!$class) $class = $gen->getInterface($class_name);
				if (!$class) {
					throw new Exception(sprintf("Class not found: %s", $class_name));
				}
				$method = $class->getMethod($method_name);
				if (!$method) {
					throw new Exception(sprintf("Method not found: %s", $method_name));
				}
				echo $method->getPHPMethod();
			} else {
				// function
				$function = $gen->getFunction($method_name);
				if (!$function) {
					throw new Exception(sprintf("Function not found: %s", $method_name));
				}
				echo $function->getPHPFunction();
			}
		} else {
			echo $gen->generateMain();
		}
	}

	public static function createDir($path)
	{
		$path = self::normalizePath($path);
		if (is_dir($path)) {
			printf("exist dir: %s\n", $path);
		} else {
			mkdir($path);
			printf("create dir: %s\n", $path);
		}
	}

	public static function putFile($path, $txt, $overwrite=true)
	{
		$path = self::normalizePath($path);
		if (is_file($path)) {
			if (isset($overwrite)) {
				if ($overwrite) {
					// yes
					file_put_contents($path, $txt);
					printf("overwrite file: %s\n", $path);
				} else {
					// no
					printf("skip: %s\n", $path);
				}
			} else {
				// ask
				printf("overwrite %s? [y/N]: ", $path);
				$in = strtolower(trim(fgets(STDIN)));
				if ($in=="y" || $in=="yes") {
					file_put_contents($path, $txt);
					printf("overwrite file: %s\n", $path);
				} else {
					printf("skip: %s\n", $path);
				}
			}
		} else {
			file_put_contents($path, $txt);
			printf("create file: %s\n", $path);
		}
	}

	public static function copyFile($path, $dir)
	{
		$path = self::normalizePath($path);
		$dir = self::normalizePath($dir);
		$dest = self::normalizePath($dir, basename($path));

		if (is_file($dest)) {
			printf("overwrite file: %s\n", $dest);
		} else {
			printf("copy file: %s\n", $dest);
		}
		copy($path, $dest);
	}

	public static function normalizePath()
	{
		$args = func_get_args();
		$func = array("self", "normalizePath");
		foreach ($args as &$arg) {
			if (is_array($arg)) {
				$arg = call_user_func_array($func, $arg);
			}
		}
		$path = implode(DIRECTORY_SEPARATOR, $args);
		$path = preg_replace("#".DIRECTORY_SEPARATOR."{2,}#", DIRECTORY_SEPARATOR, $path);
		return $path;
	}

	public static function cmd($cmd)
	{
		passthru($cmd, $ret);
		return $ret==0;
	}

	public static function fileList($dir)
	{
		$result = array();
		if ($handle = opendir($dir)) {
			while (false !== ($file = readdir($handle))) {
				if ($file !== "." && $file !== "..") {
					$file = self::normalizePath($dir, $file);
					if (is_dir($file)) {
						$result += self::fileList($file);
					} else {
						$result[] = $file;
					}
				}
			}
			closedir($handle);
		} 
		return array_values($result);
	}
}
