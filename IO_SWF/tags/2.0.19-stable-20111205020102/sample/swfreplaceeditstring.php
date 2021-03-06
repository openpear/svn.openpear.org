<?php

require_once 'IO/SWF/Editor.php';
// require dirname(__FILE__).'/../IO/SWF/Editor.php';

if ($argc != 4) {
    echo "Usage: php swfreplaceeditstring.php <swf_file> <id> <initial_text>\n";
    echo "ex) php swfreplaceeditstring.php test.swf 1 baa\n";
    echo "ex) php swfreplaceeditstring.php test.swf foo baa\n";
    exit(1);
}

assert(is_readable($argv[1]));

$swfdata = file_get_contents($argv[1]);
$id = $argv[2];
$initialText = $argv[3];

$swf = new IO_SWF_Editor();
$swf->parse($swfdata);

$ret = $swf->replaceEditString($id, $initialText);

if ($ret === false) {
    echo "failed to replaceEditString($id, $initialText)\n";
    exit(1);
}

echo $swf->build();

exit(0);
