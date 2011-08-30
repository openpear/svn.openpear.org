<?php

// 必要なファイルを取り込む
require( '../src/DecoHilighter.php' );

// 独自のClassModifier
class MyClassModifier extends DecoHighlighter_AbstractClassModifier implements DecoHighlighter_IClassModifier
{
	private $tokens;

	/**
	 *	コンストラクタ
	 */
	public function __construct()
	{
		// 通常のコメント部分とPHPDoc形式のコメントを１つのCSSクラスにまとめる
		$this->tokens = array( T_COMMENT, T_DOC_COMMENT );
	}

	/**
	 *  T_で始まるトークンに対応するCSSクラス名を指定
	 *
	 *  トークン名については下記を参照
	 *	http://www.php.net/manual/ja/tokens.php
	 */
	public function modifyToken( $token_id )
	{
		return in_array($token_id,$this->tokens) ? "my_doc" : null;
	}
}


try{

	// 読み込むソースコード
	$path = "../src/DecoHilighter.php";
	$source = file_get_contents($path);

	// DecoHighlighterインスタンスを生成
	$highlighter = new DecoHighlighter( $source, DecoHighlighter::TYPE_STRING );

	// 独自のClassModiferを追加
	$highlighter->addClassModifier( new MyClassModifier() );

	// パース
	$result = $highlighter->parse();

	// 埋め込みスタイルの追加（MyClassModifierで出力したCSSクラス名に対応）
	$style =<<< STYLE
.my_doc {
  color: darkslategray;
  font-style: italic;
  font-weight: bold;
}
STYLE;
	$result->addStyle($style);

	// HTMLを出力
	$result->render();

}
catch(Exception $e)
{
	$clazz = get_class($e);
	$message = $e->getMessage();
	print "[{$clazz}]$message";
}
