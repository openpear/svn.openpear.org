<?php

// tidyが wrap初期値68のため、
// 改行をはさむことにより、xmlnsを従来の正規表現で削除できなかったことへの対応

// htmlタグの箇所が期待値1行にならなかったとして、
// tidy config初期値を 'wrap'＝>0　にて、
// tidy通過後、htmlタグの箇所は1行で整形されて取得する。

//変更後
//php tidy.php after 

require_once 'Diggin/Scraper.php';
require_once 'Diggin/Scraper/Adapter/Htmlscraping.php';
try {
    $url = 'http://creation.mb.softbank.jp/terminal/?lup=y&cat=http';

    $scraper = new Diggin_Scraper();
    
$adapter = new Diggin_Scraper_Adapter_Htmlscraping();

if($argv[1] == 'after') {
    $adapter->setConfig(array('tidy' => array('output-xhtml' => true, 'wrap' => 0)));
} else {
    $adapter->setConfig(array('tidy' => array('output-xhtml' => true)));
}
    
$scraper->changeStrategy('Diggin_Scraper_Strategy_Flexible', $adapter);

$scraper->process('//tr[@bgcolor="#FFFFFF"]/td[1]', 'model[] => TEXT')
            ->scrape($url);
    print_r($scraper->results);
} catch (Exception $e) {
    echo $e.PHP_EOL;
}
