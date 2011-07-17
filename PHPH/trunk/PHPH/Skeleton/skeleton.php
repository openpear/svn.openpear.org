<?php

if (!ini_get('enable_dl')) {
	passthru("php -d enable_dl=On $argv[0]");
	exit;
}

if (!extension_loaded("extname")) {
	if (!dl("extname.so")) {
		echo "extension load error\n";
		exit;
	}
}

function phph_check_interface($interface)
{
	$exists = interface_exists($interface);
	printf("interface %s: %s\n", $interface, $exists ? "OK" : "ERROR");
}

function phph_check_class($class)
{
	$exists = class_exists($class);
	printf("class %s: %s\n", $class, $exists ? "OK" : "ERROR");
}

function phph_check_const($class, $const)
{
	$exists = @defined("$class::$const");
	printf("  const %s: %s\n", $const, $exists ? "OK" : "ERROR");
}

function phph_check_method($class, $method)
{
	$exists = method_exists($class, $method);
	printf("  method %s: %s\n", $method, $exists ? "OK" : "ERROR");
}

function phph_check_function($function)
{
	$exists = function_exists($function);
	printf("function %s: %s\n", $function, $exists ? "OK" : "ERROR");
}

function phph_check_define($define)
{
	$exists = defined($define);
	printf("define %s: %s\n", $define, $exists ? "OK" : "ERROR");
}

%%CHECK%%
