<?php

// PHP Extension source skeleton
class PHPH_Skeleton
{
	public static function loadConfigM4()
	{
		$path = dirname(__FILE__)."/Skeleton/config.m4.php";
		return file_get_contents($path);
	}

	public static function loadConfigW32()
	{
		$path = dirname(__FILE__)."/Skeleton/config.w32.php";
		return file_get_contents($path);
	}

	public static function loadH()
	{
		$path = dirname(__FILE__)."/Skeleton/php_skeleton.h.php";
		return file_get_contents($path);
	}

	public static function loadC()
	{
		$path = dirname(__FILE__)."/Skeleton/php_skeleton.c.php";
		return file_get_contents($path);
	}

	public static function loadMain()
	{
		$path = dirname(__FILE__)."/Skeleton/skeleton.c.php";
		return file_get_contents($path);
	}

	public static function loadDSP()
	{
		$path = dirname(__FILE__)."/Skeleton/skeleton.dsp.php";
		return file_get_contents($path);
	}

	public static function loadPHP()
	{
		$path = dirname(__FILE__)."/Skeleton/skeleton.php";
		return file_get_contents($path);
	}

	public static function loadPHPT()
	{
		$path = dirname(__FILE__)."/Skeleton/tests/001.phpt.php";
		return file_get_contents($path);
	}
}
