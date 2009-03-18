<?php
/**
 * @link http://phpize.net/
 */
require_once 'Services/Yahoo/JP/Text.php';

try {
    $yahoo = Services_Yahoo_JP_Text::factory('dependency');
    $yahoo->withAppID('www.doyouphp.jp');
    $yahoo->setSentence('うちの庭には鶏がいます。');
    $result = $yahoo->submit();

    foreach ($result as $morphem) {
        printf("%s -> %d\n", $morphem['Id'], $morphem['Dependency']);
    }
} catch (Services_Yahoo_Exception $e) {
    echo('Error.');
}
