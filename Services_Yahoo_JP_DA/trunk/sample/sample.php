<?php
/**
 * @link http://phpize.net/
 */
require_once 'Services/Yahoo/JP/DA.php';

try {
    $yahoo = Services_Yahoo_JP_DA::factory('parse');
    $yahoo->withAppID(getenv('YAHOO_APP_ID'));
    $yahoo->setSentence('うちの庭には鶏がいます。');
    $result = $yahoo->submit();

    foreach ($result as $morphem) {
        printf("%s -> %d\n", $morphem['Id'], $morphem['Dependency']);
    }
} catch (Services_Yahoo_Exception $e) {
    echo('Error.');
}
