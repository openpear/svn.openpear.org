<?php
/**
 * @link http://phpize.net/
 */
require_once 'Services/Yahoo/JP/DA.php';
require_once 'Image/GraphViz.php';

try {
    $yahoo = Services_Yahoo_JP_DA::factory('parse');
    $yahoo->withAppID(getenv('YAHOO_APP_ID'));

    $yahoo->setSentence('うちの庭には鶏がいます。');
    if (isset($_GET['s'])) {
        switch ((int)$_GET['s']) {
        case 1:
            $yahoo->setSentence('expose_phpがOnになっていて応答ヘッダにPHPのバージョンまで載っているのを見つけた時は歓喜に震えが止まらない');
            break;
        case 2:
            $yahoo->setSentence('正規表現って、プログラミング言語間の差が少ないサブ言語なのに、なぜ「PHP」がつくとダメ正規表現ばかり登場するのか。');
            break;
        }
    }
    $result = $yahoo->submit();

    $graph = new Image_GraphViz();
    foreach ($result as $morphem) {
        $str = null;
        foreach ($morphem['MorphemList']->Morphem as $obj) {
            $str .= $obj->Surface;
        }
        $graph->addNode($morphem['Id'], array('label' => $str));
        if ($morphem['Dependency'] !== '-1') {

            $graph->addEdge(array($morphem['Id'] => $morphem['Dependency']));
        }
    }

    $url = '?s=' . (isset($_GET['s']) ? ((int)$_GET['s'] + 1) : 1);
    $graph->addNode('next', array('label' => 'next', 'URL' => $url));

    $graph->image();
} catch (Services_Yahoo_Exception $e) {
    die($e->getMessage());
}
