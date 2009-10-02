<?php
require_once 'Mac/AppleScript.php';

$as = new Mac_AppleScript();
echo $as->say('Hello, "AppleScript"!', Mac_AppleScript::OPTION_EXECUTE), "\n";
