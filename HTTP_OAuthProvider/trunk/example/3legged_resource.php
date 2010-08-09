<?php
require_once('HTTP/OAuthProvider.php');
require_once('config.php');

$o = new HTTP_OAuthProvider();
try {
	$o->setFindConsumerHandler('findConsumer');
	$o->authenticate3L();
	echo "this is provider's protected resource !!\n";
	printf("consumer: %s\n", $o->getConsumer()->getKey());
	printf("user_id: %s\n", $o->getStore()->getUserID());

} catch(Exception $e) {
	header(sprintf('HTTP/1.0 %d', $e->getCode()));
	echo $e->getMessage();
}
