<?php
ini_set('include_path', '..' . DIRECTORY_SEPARATOR . PATH_SEPARATOR . ini_get('include_path'));

/**
 * this sample require PEAR::HTTP_Request2
 */
require_once 'HTTP/Request2.php';
require_once 'Auth/WSSE.php';

define('AUTH_USER', 'user1');
define('AUTH_PASSWORD', 'pass1');

/**
 * request with X-WSSE header and output result
 */
function send_wsse_request($user, $passwd) {
    if (is_null($user) || $user === '' || is_null($passwd) || $passwd === '') {
        echo 'parameter error';
        return;
    }

    $wsse = new Auth_WSSE($user, $passwd);
    $request = new HTTP_Request2();
    $request->setUrl('http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']);
    $request->setHeader('X_WSSE', $wsse->getHeader());
    try {
        $response = $request->send();
        switch ($response->getStatus()) {
        case 200:
            echo $response->getBody();
        }
    } catch (Exception $e) {
        echo 'failed to request : ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, mb_internal_encoding());
    }
}

/**
 * execute authenticate
 */
function auth($header) {
    $result = Auth_WSSE::parseHeader($header);
    $wsse = new Auth_WSSE(AUTH_USER, AUTH_PASSWORD, base64_decode($result['nonce']), $result['created']);
    echo ($wsse->getDigest() === $result['digest']) ? 'success' : 'failed';
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['user']) && isset($_POST['passwd'])) {
    send_wsse_request($_POST['user'], $_POST['passwd']);
} else if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_SERVER['HTTP_X_WSSE'])) {
    auth($_SERVER['HTTP_X_WSSE']);
}
?>
<form action="" method="post">
<p>Username:<input type="text" name="user" value=""></p>
<p>Password:<input type="password" name="passwd" value=""></p>
<p><input type="submit"></p>
</form>
