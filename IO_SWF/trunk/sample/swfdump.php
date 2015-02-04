<?php

require 'IO/SWF.php';
// require dirname(__FILE__).'/../IO/SWF.php';

$options = getopt("f:hl");

if ((isset($options['f']) === false) || (is_readable($options['f']) === false)) {
    echo "Usage: php swfdump.php -f <swf_file> [-h] [-l]\n";
    echo "ex) php swfdump.php -f test.swf -h -l\n";
    exit(1);
}

$swfdata = file_get_contents($options['f']);

$swf = new IO_SWF();

$swf->parse($swfdata);

$opts = array();
if (isset($options['h'])) {
    $opts['hexdump'] = true;
}
if (isset($options['l'])) {
    $opts['addlabel'] = true;
}

$swf->dump($opts);

exit(0);
