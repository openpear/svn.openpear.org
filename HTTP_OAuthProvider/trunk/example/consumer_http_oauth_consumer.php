<?php
// require HTTP_OAuth
require_once('HTTP/OAuth/Consumer.php');

/* 認証情報を格納するためにセッションを開始 */
session_start();

/* Consumer key */
$consumer_key = 'testconsumer';
/* Consumer Secret */
$consumer_secret = 'testpass';

/* プロバイダからの Callback url */
$callback_url = sprintf('http://%s%s', $_SERVER['HTTP_HOST'], $_SERVER['SCRIPT_NAME']);

$provider_base = 'http://example.com/example/';
$request_url = $provider_base.'3legged_request.php';
$authorize_url = $provider_base.'3legged_authorize.php';
$access_url = $provider_base.'3legged_access.php';
$resource_url = $provider_base.'3legged_resource.php';


/* Set up placeholder */
$content = '';

/* セッションのクリア */
if (@$_REQUEST['test'] === 'clear') {
    session_destroy();
	$_SESSION = array();
    session_start();
}

try {

	// -- init HTTP_OAuth_Consumer
	$oauth = new HTTP_OAuth_Consumer($consumer_key, $consumer_secret);
    // ssl通信を可能に
    $http_request = new HTTP_Request2();
    $http_request->setConfig('ssl_verify_peer', false);
    $consumer_request = new HTTP_OAuth_Consumer_Request;
    $consumer_request->accept($http_request);
    $oauth->accept($consumer_request);

    if (!empty($_REQUEST['oauth_token']) && $_SESSION['oauth_state'] === 'start') {
        // -- プロバイダから認証後に戻ってきた場合 (callback処理)
        $_SESSION['oauth_state'] = 'returned';

        if (empty($_SESSION['oauth_access_token']) || empty($_SESSION['oauth_access_token_secret'])) {
            // -- access_tokenが未取得の場合
            /* request tokenをセット */
            $oauth->setToken($_SESSION['oauth_request_token']);
            $oauth->setTokenSecret($_SESSION['oauth_request_token_secret']);

            /* プロバイダから戻ってきた oauth_verifierをセット */
            $oauth_verifier = $_REQUEST['oauth_verifier'];

            /* Access token をリクエスト */
            $oauth->getAccessToken($access_url, $oauth_verifier);

            /* Acces tokenを保存 (実際のアプリケーションではこれをDB等に保存しておきます。) */
            $_SESSION['oauth_access_token'] = $oauth->getToken();
            $_SESSION['oauth_access_token_secret'] = $oauth->getTokenSecret();
        }

    }

    if (!empty($_SESSION['oauth_access_token']) && !empty($_SESSION['oauth_access_token_secret'])) {
        // -- 認証済みの場合

        /* access_tokenをセット */
        $oauth->setToken($_SESSION['oauth_access_token']);
        $oauth->setTokenSecret($_SESSION['oauth_access_token_secret']);

        /* ユーザ情報を取得するリクエストを発行. */
        $result = $oauth->sendRequest($resource_url, array(), 'GET');

        /* データを取得 */
        $content = $result->getBody();

    } else {

        // -- 初回呼び出し時
        /* プロバイダからrequest_tokenの取得 */
        $oauth->getRequestToken($request_url, $callback_url);

        /* tokenをセッションに保存 */
        $_SESSION['oauth_request_token'] = $oauth->getToken();
        $_SESSION['oauth_request_token_secret'] = $oauth->getTokenSecret();
        /* ステータスをstartにセット */
        $_SESSION['oauth_state'] = "start";

        /* authorization URL を取得 */
        $request_link = $oauth->getAuthorizeURL($authorize_url);

        /* authorization URLのリンクを作成 */
        $content = 'Click on the link to go to provider to authorize your account.';
        $content .= '<br /><a href="'.$request_link.'">'.$request_link.'</a>';

    }

} catch (Exception $e) {
    $content = $e->getMessage();
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
