<?php
ini_set("include_path",  dirname(__FILE__) . "/../src".PATH_SEPARATOR.ini_get("include_path"));
require_once 'Net/Cicindela.php';

$config = include('./config.php');
$cicindela = new Net_Cicindela($config['base_url']);

$clipRecommender = $cicindela->getDataset('clip_simple');
foreach($clipRecommender->getRecommendForUser(123, 5) AS $item) {
    echo $item, PHP_EOL;
}
?>
