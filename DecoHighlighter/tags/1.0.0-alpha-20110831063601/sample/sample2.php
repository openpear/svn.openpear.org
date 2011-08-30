<?php

// 必要なファイルを取り込む
require( '../src/DecoHilighter.php' );

try{

	// 読み込むソースコード
	$path = "../src/DecoHilighter.php";

	// ファイルからパースしてHTMLを出力（HTMLヘッダあり）
	DecoHighlighter::parseFromFile($path)->render();

}
catch(Exception $e)
{
	$clazz = get_class($e);
	$message = $e->getMessage();
	print "[{$clazz}]$message";
}
