<?php

// 必要なファイルを取り込む
require( '../src/DecoHilighter.php' );

try{

	// 読み込むソースコード
	$path = "../src/DecoHilighter.php";
	$source = file_get_contents($path);

	// 文字列からパースしてHTMLを出力（HTMLヘッダあり）
	DecoHighlighter::parseFromString($source)->render();

}
catch(Exception $e)
{
	$clazz = get_class($e);
	$message = $e->getMessage();
	print "[{$clazz}]$message";
}
