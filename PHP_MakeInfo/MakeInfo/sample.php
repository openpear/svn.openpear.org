<?php
require_once("MakeInfo.php");

$contents = array(
	'Sample1' => 'Enable1',
	'Sample2' => 'Enable2',
	'Sample3' => 'Enable3',
	'Sample4' => 'Enable4',
	'Sample5' => 'Enable5',
);

$make = new MakeInfo("Sample", "Sample Enables List", $contents);
?>