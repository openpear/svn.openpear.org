<?php
/**
 * @link http://phpize.net/
 */
require_once 'Services/Yahoo/JP/Furigana.php';

try {
    $yahoo = Services_Yahoo_JP_Furigana::factory('furigana');
    $yahoo->withAppID(getenv('YAHOO_APP_ID'));
    $yahoo->setSentence('うちの庭には鶏がいます。');
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
