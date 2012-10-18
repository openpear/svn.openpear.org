<?php

require_once 'IO/MIDI.php';

$mididata = file_get_contents($argv[1]);
$midi = new IO_MIDI();
$midi->parse($mididata);
$midi->dump();


