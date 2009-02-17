<?php
// $Id$

require_once 'Keires/FeedParser.php';

function usage() {
    echo "Usage: program feed_url\n";
}

try {
    if ($argc != 2) {
        usage();
        exit;
    }

    $url = $argv[1];
    $xml = file_get_contents($url);

    $parser = new Keires_FeedParser($xml);
    $parser->parse();
    var_dump($parser->getFeed());

} catch (Exception $e) {
    die($e->getMessage());
}

?>