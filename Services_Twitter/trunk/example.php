<?php
// Twitterライブラリの読み込み
require_once('Services/Twitter.php');

// Services_Twitterの設定情報
$config = new Services_Twitter_Config();

// アプリケーション認識キーの設定
$config->setApplicationKey(sha1('Services_Twitter Example Code'));

// 登録情報のCookieキーの設定
$config->setCookieName('services_twitter_id');

// コンシューマキーの設定
// テスト値：Services_Twitterのコンシューマキーを利用
$config->setConsumer('Rzt2HVOtG1TmcgtE8r1rQ','DDHJGoElzijet5AWsNWkazAQvhVDaoxSqru20oDrdM');

// トークンの読み込み・書き込み処理を登録
$config->setTokenReadFunction('readTokens');
$config->setTokenSaveFunction('saveTokens');

// 認証ページ、認証コールバックURIの設定
$config->setAuthPage(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'example.template.php');
$config->setCallback('http://localhost/stw/trunk/example.php');

// Services_Twitterのインスタンス化
$tweet = new Services_Twitter($config);

// OAuthにて接続を行う。
//未認証時は認証用のページが表示される。
$tweet->connect();

// 認証情報の設定
if ($tweet->isAuthorized()) {
    // APIの利用状況を取得する。
    $limit = $tweet->getRateLimitStatus();
    debug($limit);
}

/**
 * 保存したトークンを取得する
 */
function readTokens() {
    $authfile = dirname(__FILE__) . DIRECTORY_SEPARATOR . '.twitter';
    if (file_exists($authfile)) {
        return unserialize(file_get_contents($authfile));
    }

    return null;
}

/**
 * トークンを保存する
 */
function saveTokens($tokens) {
    $authfile = dirname(__FILE__) . DIRECTORY_SEPARATOR . '.twitter';
    file_put_contents($authfile, serialize($tokens));
}

function debug($value) {
	echo '<pre>';
	var_dump($value);
	echo '</pre><hr />';
}

?>