<?php
require_once('HTTP/OAuthConsumer.php');

session_start();

// Provider info
$provider_base = sprintf('http://%s%s', $_SERVER['HTTP_HOST'], dirname($_SERVER['SCRIPT_NAME']));
$provider_base = rtrim($provider_base, '/').'/';
$request_url = $provider_base.'3legged_request.php';
$authorize_url = $provider_base.'3legged_authorize.php';
$access_url = $provider_base.'3legged_access.php';
$resource_url = $provider_base.'3legged_resource.php';

// Consumer info
$consumer_key = 'testconsumer';
$consumer_secret = 'testpass';
$callback_url = sprintf('http://%s%s', $_SERVER['HTTP_HOST'], $_SERVER['SCRIPT_NAME']);

// Session clear
if (@$_REQUEST['test'] === 'clear') {
	session_destroy();
	$_SESSION = array();
	session_start();
}

$content = '';

try {
	// Initialize HTTP_OAuthConsumer
	$oauth = HTTP_OAuthConsumer::factory();
	$oauth->setConsumer($consumer_key, $consumer_secret);

	if (!isset($_SESSION['type'])) {
		$_SESSION['type'] = null;
	}

	// 2 Authorize
	if ($_SESSION['type']=='authorize' && isset($_GET['oauth_token'], $_GET['oauth_verifier'])) {
		// Exchange the Request Token for an Access Token
		$oauth->setURL($access_url);
		$oauth->setToken($_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
		$access = $oauth->getAccessToken($_GET['oauth_verifier']);

		// Save an Access Token
		$_SESSION['type'] = 'access';
		$_SESSION['oauth_token'] = $access['oauth_token'];
		$_SESSION['oauth_token_secret'] = $access['oauth_token_secret'];
	}

	// 3 Access
	if ($_SESSION['type']=='access') {
		// Accessing Protected Resources
		$oauth->setURL($resource_url);
		$oauth->setToken($_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
		$result = $oauth->send();

		$content = $result->getBody();

	// 1 Request
	} else {
		// Get a Request Token
		$oauth->setURL($request_url);
		$request = $oauth->getRequestToken($callback_url);

		// Save a Request Token
		$_SESSION['type'] = 'authorize';
		$_SESSION['oauth_token'] = $request['oauth_token'];
		$_SESSION['oauth_token_secret'] = $request['oauth_token_secret'];

		// Get an Authorize URL
		$authorize_url = $oauth->getAuthorizeURL($authorize_url);

		$content = "Click on the link to go to provider to authorize your account.<br />\n";
		$content .= sprintf('<a href="%s">%s</a>', $authorize_url, $authorize_url);
	}

} catch (Exception $e) {
	$content .= $e->getMessage();
}
?>
<html>
<head>
<title>OAuth in PHP</title>
</head>
<body>
<h2>Welcome to a OAuth PHP example.</h2>
<p><a href='?test=clear'>clear sessions</a></p>

<p><pre><?php print_r($content); ?><pre></p>

</body>
</html>

