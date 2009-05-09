<?php
$base_dir = dirname(__FILE__) . '/';
require_once realpath($base_dir .'../HTML/CSS/Mobile.php'); // 開発用
//require_once 'HTML/CSS/Mobile.php';

$document = file_get_contents(realpath($base_dir.'sample.html'));
try {
	echo HTML_CSS_Mobile::getInstance()->setBaseDir($base_dir)->setMode('strict')->addCSSFiles(array('sample3.css', 'sample4.css'))->addCSSFiles('sample5.css')->apply($document);
} catch (RuntimeException $e) {
	var_dump($e);
} catch (Exception $e) {
	var_dump($e->getMessage());
}

