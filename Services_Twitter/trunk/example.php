<?php
// Twitterライブラリの読み込み
require_once('Services/Twitter.php');

// Services_Twitterの設定情報
$config = new Services_Twitter_Config();

// コンシューマキーの設定
// テスト値：Services_Twitterのコンシューマキーを利用
$config->setConsumer('Rzt2HVOtG1TmcgtE8r1rQ','DDHJGoElzijet5AWsNWkazAQvhVDaoxSqru20oDrdM');

// 認証情報ファイルのパス設定
$config->setAuthFile(dirname(__FILE__) . DIRECTORY_SEPARATOR . '.twitter');

// 認証ページ、認証コールバックURIの設定
$config->setAuthPage(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'example.template.php');
$config->setCallback('http://localhost/stw/trunk/example.php');


// Services_Twitterのインスタンス化
$tweet = new Services_Twitter($config);

// OAuthにて接続を行う。
//未認証時は認証用のページが表示される。
$tweet->connect();

// APIの利用状況を取得する。
$limit = $tweet->getRateLimitStatus();
debug($limit);


function debug($value) {
	echo '<pre>';
	var_dump($value);
	echo '</pre><hr />';
}

?>