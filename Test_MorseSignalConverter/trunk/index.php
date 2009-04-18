<?php
	ini_set( "display_errors", "on" );

	include "MorseSignalConverter.php";

	$str = 'Hello, World';
	echo $str . "=> <BR>\n";
	echo test_MorseSignalConverter( $str ) . "\n";
	echo "<BR>\n";

	$str = 'コンニチハ、セカイ';
	echo $str . "=> <BR>\n";
	echo test_MorseSignalConverter( $str, 'UTF-8' );
	echo "<BR>\n";

?>
