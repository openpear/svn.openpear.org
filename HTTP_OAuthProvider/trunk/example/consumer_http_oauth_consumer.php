<?php
require_once('HTTP/OAuthConsumer.php');

session_start();

// provider url
$request_url = 'http://eth0.jp/~tetu/php10/100805_01_HTTP_OAuthProvider/3legged_request.php';
$authorize_url = 'http://eth0.jp/~tetu/php10/100805_01_HTTP_OAuthProvider/3legged_authorize.php';
$access_url = 'http://eth0.jp/~tetu/php10/100805_01_HTTP_OAuthProvider/3legged_access.php';

// consumer url
$callback_url = 'http://eth0.jp/~tetu/php10/100805_01_HTTP_OAuthProvider/cli1.php';


try {
	$oauth = HTTP_OAuthConsumer::factory();
	$oauth->setConsumer('testconsumer', 'testpass');

	$type = isset($_SESSION['type']) ? $_SESSION['type'] : null;

	// 2 認可をもらって帰ってきた
	if ($type=='authorize' && isset($_GET['oauth_token'], $_GET['oauth_verifier'])) {

		// exchange access token
		$oauth->setURL($access_url);
		$oauth->setToken($_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
		$oauth->setVerifier($_GET['oauth_verifier']);
		$res = $oauth->send();
		echo "Access token:<br /><pre>\n";
		print_r($res);
		print_r($oauth);
		echo "<pre>\n";

		//$_SESSION['type'] = 'access';

	// 1 認可をもらいに飛ぶ
	} else {
		// request token
		$oauth->setURL($request_url);
		$oauth->addGetParameter('oauth_callback', $callback_url);
		$res = $oauth->send();
		if ($res->getStatus()!=200) {
			$message = sprintf('Response status error: %s', $res->getBody());
			throw new Exception($message);
		}
		parse_str($res->getBody(), $req);
		if (!isset($req['oauth_token'], $req['oauth_token_secret'])) {
			$message = sprintf('Response body error: %s', $res->getBody());
			throw new Exception($message);
		}
		$_SESSION['type'] = 'authorize';
		$_SESSION['oauth_token'] = $req['oauth_token'];
		$_SESSION['oauth_token_secret'] = $req['oauth_token_secret'];

		// authorize url
		$authorize_url .= sprintf('?oauth_token=%s', $req['oauth_token']);
		echo "Authorize URL:<br />\n";
		printf('<a href="%s">%s</a>', $authorize_url, $authorize_url);
		echo "<br />\n";
	}

} catch(Exception $e) {
	echo $e->getMessage();
}

