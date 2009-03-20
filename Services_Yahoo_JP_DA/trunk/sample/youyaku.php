#!/usr/local/lib/php5/bin/php
<?php
/**
 * @link http://chalow.net/2008-08-21-1.html
 */
require_once 'Services/Yahoo/JP/DA.php';

$stdin = fopen('php://stdin', 'r');
while (($line = trim(fgets(STDIN))) !== false) {
    echo $line . PHP_EOL;
    try {
        $yahoo = Services_Yahoo_JP_DA::factory('parse');
        $yahoo->withAppID(getenv('YAHOO_APP_ID'));
        $yahoo->setSentence($line);
        $result = $yahoo->submit();

        $dependency = array();
        $morphs = array();
        $base_id = null;
        foreach ($result as $data) {
            $id = $data['Id'];
            if ($data['Dependency'] >= 0) {
                $dependency[$data['Dependency']][] = $id;
            } else {
                $base_id = $id;
            }
            $morphs[$id] = null;
            foreach ($data['MorphemList'] as $morphem) {
                $morphs[$id] .= $morphem->Surface;
            }
        }

        $str = null;
        foreach ($dependency[$base_id] as $id) {
            $str .= $morphs[$id];
        }
        echo 'SUMMARY:' . $str . $morphs[$base_id] . PHP_EOL;

    } catch (Services_Yahoo_Exception $e) {
        die($e->getMessage());
    }
}
