<?php
require_once('HTTP/OAuthConsumer.php');

session_start();

// provider url
$provider_base = sprintf('http://%s%s', $_SERVER['HTTP_HOST'], dirname($_SERVER['SCRIPT_NAME']));
$provider_base = rtrim($provider_base, '/').'/';
$request_url = $provider_base.'3legged_request.php';
$authorize_url = $provider_base.'3legged_authorize.php';
$access_url = $provider_base.'3legged_access.php';
$resource_url = $provider_base.'3legged_resource.php';

// consumer url
$callback_url = sprintf('http://%s%s', $_SERVER['HTTP_HOST'], $_SERVER['SCRIPT_NAME']);

/* セッションのクリア */
if (@$_REQUEST['test'] === 'clear') {
	session_destroy();
	session_start();
}

$content = '';

try {
	$oauth = HTTP_OAuthConsumer::factory();
	$oauth->setConsumer('testconsumer', 'testpass');

	if (!isset($_SESSION['type'])) {
		$_SESSION['type'] = null;
	}

	// 2 認可をもらって帰ってきた
	if ($_SESSION['type']=='authorize' && isset($_GET['oauth_token'], $_GET['oauth_verifier'])) {

		// リクエストトークンをアクセストークンに交換
		$oauth->setURL($access_url);
		$oauth->setToken($_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
		$access = $oauth->getAccessToken($_GET['oauth_verifier']);

		// アクセストークンを保存
		$_SESSION['type'] = 'access';
		$_SESSION['oauth_token'] = $access['oauth_token'];
		$_SESSION['oauth_token_secret'] = $access['oauth_token_secret'];
	}

	// 3 保護されたリソースへアクセス
	if ($_SESSION['type']=='access') {
		// 保護されたリソースへリクエストを送る
		$oauth->setURL($resource_url);
		$oauth->setToken($_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
		$res = $oauth->send();

		// データを取得
		$content = $res->getBody();

	// 1 認可をもらいに飛ぶ
	} else {
		// プロバイダからリクエストトークンの取得
		$oauth->setURL($request_url);
		$req = $oauth->getRequestToken($callback_url);

		// リクエストトークンをセッションに保存
		$_SESSION['type'] = 'authorize';
		$_SESSION['oauth_token'] = $req['oauth_token'];
		$_SESSION['oauth_token_secret'] = $req['oauth_token_secret'];

		// authorize urlを取得
		$authorize_url = $oauth->getAuthorizeURL($authorize_url);

		// authorize urlのリンク作成
		$content = "Click on the link to go to provider to authorize your account.<br />\n";
		$content .= sprintf('<a href="%s">%s</a>', $authorize_url, $authorize_url);
	}

} catch(Exception $e) {
	$content .= $e->getMessage();
}
?>
<html>
<head>
<title>OAuth in PHP</title>
</head>
<body>
<h2>Welcome to a OAuth PHP example.</h2>
<p><a href='<?php echo $_SERVER['PHP_SELF']; ?>?test=clear'>clear sessions</a></p>

<p><pre><?php print_r($content); ?><pre></p>

</body>
</html>

