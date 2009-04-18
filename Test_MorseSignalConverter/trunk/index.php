<?php
	ini_set( "display_errors", "on" );


	include "MorseSignalConverter.php";

	$str = 'Hello, World';
	echo test_MorseSignalConverter( $str );
?>
