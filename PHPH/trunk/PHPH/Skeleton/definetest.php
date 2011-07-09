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

function check($type, $name)
{
	$check_func = $type."_exists";
	$exists = $check_func($name);
	printf("%s %s: %s\n", $type, $name, $exists ? "OK" : "ERROR");
}

%%CHECK%%
