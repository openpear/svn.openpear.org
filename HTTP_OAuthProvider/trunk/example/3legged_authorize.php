<?php
require_once('HTTP/OAuthProvider.php');
require_once('config.php');

$content = "<b>this is provider's page !!</b><br />\n";
$o = new HTTP_OAuthProvider();
try {
	$o->setGetConsumerHandler('getConsumer');

	// check request token
	if (!$o->existsRequestToken()) {
		throw new Exception('not found request token', 200);
	}

	// show callback url
	if (isset($_REQUEST['authorize_confirm'])) {
		if ($_REQUEST['authorize_confirm']) {
			$callback = $o->authorizeToken($user_id, true);
			$content .= "you choose agree<br />\n";
		} else {
			$callback = $o->authorizeToken($user_id, false);
			$content .= "you choose disagree<br />\n";
		}
		$content .= "return to consumer's page<br />\n";
		$content .= sprintf('<a href="%s">%s</a>', $callback, $callback)."<br />\n";

	// show form
	} else {
		$content .= sprintf("hello. user id %s !<br />\n", $user_id);
		$content .= "do you authorize the consumer?<br />\n";
		$content .= sprintf("consumer is %s<br />\n", $o->getConsumer()->getKey());

		// agree form
		$content .= '<form action="?" method="post">'."\n";
		foreach ($_REQUEST as $key=>$value) {
			$content .= sprintf('<input type="hidden" name="%s" value="%s" />', $key, $value)."\n";
		}
		$content .= '<input type="hidden" name="authorize_confirm" value="1" />'."\n";
		$content .= sprintf('<input type="submit" value="agree">')."\n";
		$content .= "</form>\n";

		// disagree form
		$content .= '<form action="?" method="post">'."\n";
		foreach ($_REQUEST as $key=>$value) {
			$content .= sprintf('<input type="hidden" name="%s" value="%s" />', $key, $value)."\n";
		}
		$content .= '<input type="hidden" name="authorize_confirm" value="0" />'."\n";
		$content .= sprintf('<input type="submit" value="disagree">')."\n";
		$content .= "</form>\n";
	}

} catch(Exception $e) {
	header(sprintf('HTTP/1.0 %d', $e->getCode()));
	$content .= $e->getMessage();
}

?>

<html>
<head>
<title>provider's page</title>
</head>
<body>
<?php echo $content; ?>
</body>
<html>
