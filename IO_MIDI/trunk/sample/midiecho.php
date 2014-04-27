<?php

require_once 'IO/MIDI.php';

$options = getopt("f:h");

if (($argc !== 2) || (! is_readable($argv[1]))) {
    echo "Usage: php midiecho.php <midi_file> \n";
    echo "ex) php midiecho.php in.mid\n";
    exit(1);
}

$midiin = file_get_contents($argv[1]);

$midi = new IO_MIDI();
$midi->parse($midiin);

echo $midi->build();
