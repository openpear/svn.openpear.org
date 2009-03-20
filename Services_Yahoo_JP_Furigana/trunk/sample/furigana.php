#!/usr/local/lib/php5/bin/php
<?php
require_once 'Services/Yahoo/JP/Furigana.php';

$stdin = fopen('php://stdin', 'r');
while (($line = trim(fgets(STDIN))) !== false) {
    echo $line . PHP_EOL;
    try {
        $yahoo = Services_Yahoo_JP_Furigana::factory('furigana');
        $yahoo->withAppID(getenv('YAHOO_APP_ID'));
        $yahoo->setSentence($line);
        $yahoo->setGrade(1);
        $result = $yahoo->submit();

        $str = null;
        foreach ($result as $word) {
            $str .= isset($word['Furigana']) ? $word['Furigana'] : $word['Surface'];
        }
        echo $str . PHP_EOL;
    } catch (Services_Yahoo_Exception $e) {
        die($e->getMessage());
    }
}
