<?php
require_once('HTTP/OAuthConsumer.php');

try {
	$oauth = HTTP_OAuthConsumer::factory();
	$oauth->setURL('http://example.com/?format=json');
	$oauth->setMethod('POST');
	$oauth->addPostParameter('aaa', 'AAA');
	$oauth->setConsumer('testuser', 'testpass');
	$res = $oauth->send();
	print_r($res);
} catch(Exception $e) {
	echo $e->getMessage();
}
echo "\n";

