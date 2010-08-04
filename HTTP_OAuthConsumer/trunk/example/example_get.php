<?php
require_once('HTTP/OAuthConsumer.php');

try {
	$oauth = HTTP_OAuthConsumer::factory();
	$oauth->setURL('http://example.com/?format=json');
	$oauth->addGetParameter('aaa', 'AAA');
	$oauth->setConsumer('testuser', 'testpass');
	$res = $oauth->send();
	print_r($res);
} catch(Exception $e) {
	echo $e->getMessage();
}
echo "\n";

