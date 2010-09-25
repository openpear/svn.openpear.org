<?php
require_once('HTTP/OAuth/Consumer.php');

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
	// Initialize HTTP_OAuth_Consumer
	$oauth = new HTTP_OAuth_Consumer($consumer_key, $consumer_secret);

	// Enable SSL
	$http_request = new HTTP_Request2();
	$http_request->setConfig('ssl_verify_peer', false);
	$consumer_request = new HTTP_OAuth_Consumer_Request;
	$consumer_request->accept($http_request);
	$oauth->accept($consumer_request);

	if (!isset($_SESSION['type'])) {
		$_SESSION['type'] = null;
	}

	// 2 Authorize
	if ($_SESSION['type']=='authorize' && isset($_GET['oauth_token'], $_GET['oauth_verifier'])) {
		// Exchange the Request Token for an Access Token
		$oauth->setToken($_SESSION['oauth_token']);
		$oauth->setTokenSecret($_SESSION['oauth_token_secret']);
		$oauth->getAccessToken($access_url, $_GET['oauth_verifier']);

		// Save an Access Token
		$_SESSION['type'] = 'access';
		$_SESSION['oauth_token'] = $oauth->getToken();
		$_SESSION['oauth_token_secret'] = $oauth->getTokenSecret();
	}

	// 3 Access
	if ($_SESSION['type']=='access') {
		// Accessing Protected Resources
		$oauth->setToken($_SESSION['oauth_token']);
		$oauth->setTokenSecret($_SESSION['oauth_token_secret']);
		$result = $oauth->sendRequest($resource_url, array(), 'GET');

		$content = $result->getBody();

	// 1 Request
	} else {
		// Get a Request Token
		$oauth->getRequestToken($request_url, $callback_url);

		// Save a Request Token
		$_SESSION['type'] = 'authorize';
		$_SESSION['oauth_token'] = $oauth->getToken();
		$_SESSION['oauth_token_secret'] = $oauth->getTokenSecret();

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
