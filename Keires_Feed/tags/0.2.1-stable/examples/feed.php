<?php
// $Id$

require_once 'Keires/Feed.php';

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

    $opt = array(
        'noreq' => true,
        );
    $feed = new Keires_Feed(null, $opt);
    $feed->setContents($xml);

    $feed->parse();

    $items = $feed->getItems();

    foreach ($items as $item) {
        var_dump($item);
    }

} catch (Exception $e) {
    die($e->getMessage());
  }



?>