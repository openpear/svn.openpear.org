<?php

$page_html = file_get_contents('hello.html');
$page_css = file_get_contents('hello.css');

$a = "{";
$b = "}";

$c = strpos($page_css, $a);
if (false !== $c) {
	$d = strpos($page_css, $b, $c);
	var_dump (substr($page_css, $c, $d));
}

//var_dump($a);

?>
