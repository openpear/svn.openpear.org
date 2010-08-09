<?php
require_once('HTTP/OAuthProvider.php');
require_once('config.php');

$o = new HTTP_OAuthProvider();
try {
	$o->setFindConsumerHandler('findConsumer');
	echo $o->exchangeAccessToken();

} catch(Exception $e) {
	header(sprintf('HTTP/1.0 %d', $e->getCode()));
	echo $e->getMessage();
}
