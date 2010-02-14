<?php
/**
 * example.php
 *
 */

require_once 'Services/Bitly.php';

$login = 'Bitlyのアカウント';
$apikey = 'BitlyのAPI Key';

try {
    $bitly = new Services_Bitly($login,$apikey);
    $shorten = $bitly->shorten("http://openpear.org/package/Services_Bitly");
} catch (Services_Bitly_Exception $e) {
    echo $e->getMessage();
}

try {
    $bitly = new Services_Bitly($login,$apikey);
    $expand = $bitly->expand($shorten);
} catch (Services_Bitly_Exception $e) {
    echo $e->getMessage();
}



// j.mp対応

try {
    $bitly = new Services_Bitly($login,$apikey);
    $bitly->setBaseDomain('j.mp');
    $shorten = $bitly->shorten("http://openpear.org");
} catch (Services_Bitly_Exception $e) {
    echo $e->getMessage();
}

try {
    $bitly = new Services_Bitly($login,$apikey);
    $bitly->setBaseDomain('j.mp');
    $expand = $bitly->expand($shorten);
} catch (Services_Bitly_Exception $e) {
    echo $e->getMessage();
}

