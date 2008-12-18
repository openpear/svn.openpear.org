<?php
ini_set("include_path", dirname(__FILE__)."/..//src/" . PATH_SEPARATOR . ini_get("include_path"));
require_once "Services/Tumblr.php";

// Test
$email = "youremail@example.com";
$password = "yourpassword";
$tumblr = new Services_Tumblr($email, $password);

// post regular
$tumblr->Regular->title = "post title!!";
$tumblr->Regular->body = "post body!!! post body!!!";
$tumblr->Regular->write();


// read


?>
