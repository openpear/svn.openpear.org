<?php
require_once('HTTP/OAuthConsumer.php');

try {
	$privatekey = file_get_contents('private.key');
	$oauth = HTTP_OAuthConsumer::factory('RSA-SHA1');
	$oauth->setURL('http://example.com/?format=json');
	$oauth->setMethod('POST');
	$oauth->addPostParameter('aaa', 'AAA');
	$oauth->setConsumer('testuser', $privatekey);
	$res = $oauth->send();
	print_r($res);
} catch(Exception $e) {
	echo $e->getMessage();
}
echo "\n";

