<?php

// consumer

function getConsumer($consumer_key)
{
	$row = array(
		'id' => 1,
		'key' => 'testconsumer',
		'secret' => 'testpass',
		'publickey' => null
	);
	if ($consumer_key==$row['key']) {
		$consumer = new HTTP_OAuthProvider_Consumer($row);
		return $consumer;
	}
}


// user

$user_id = 12345;
