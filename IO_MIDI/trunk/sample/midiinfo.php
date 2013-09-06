<?php

require_once 'IO/MIDI.php';

$options = getopt("f:");

if ((isset($options['f']) === false) || (is_readable($options['f']) === false)) {
    echo "Usage: php midiinfo.php -f <midi_file>\n";
    echo "ex) php midiinfo.php -f test.mid\n";
    exit(1);
}

$mididata = file_get_contents($options['f']);

$midi = new IO_MIDI();
$midi->parse($mididata);

$header = $midi->header['header'];
$tracks = $midi->tracks;
echo "Header:".PHP_EOL;
echo "  NumberOfTracks: ".$header['NumberOfTracks'].PHP_EOL;
echo "  Division: ".$header['Division'].PHP_EOL;
echo "Tracks:".PHP_EOL;
foreach ($tracks as $idx => $track) {
    $channelTable = array();
    $programTable = array();
    $noteOnTable = array();
    $noteOffTable = array();
    $noteOnTable = array();
    $noteKeyTable = array();
    $pitchbendTable =  array();
    //
    echo "  Track[$idx]:".PHP_EOL;
    foreach ($track['track'] as $chunk) {
        if ($chunk['EventType'] != 0xF) {
            $channel = $chunk['MIDIChannel'];
            if (isset($channelTable[$channel]) === false) {
                $channelTable[$channel] = true;
                if (isset($noteOnTable[$channel]) === false) {
                    $noteOnTable[$channel] = 0;
                }
                if (isset($noteOffTable[$channel]) === false) {
                    $noteOffTable[$channel] = 0;
                }
                if (isset($noteKeyTable[$channel]) === false) {
                    $noteKeyTable[$channel] = array();
                }
                if (isset($pitchbendTable[$channel]) === false) {
                    $pitchbendTable[$channel]) = array();
                }
            }
            switch ($chunk['EventType']) {
              case 0x8: // Note Off
                $noteOffTable[$channel] ++;
                break;
              case 0x9: // Note On
                $noteOnTable[$channel] ++;
                if (isset($noteKeyTable[$channel][$chunk['NoteNumber']])) {
                    $noteKeyTable[$channel][$chunk['NoteNumber']] ++;
                } else {
                    $noteKeyTable[$channel][$chunk['NoteNumber']] = 1;
                }
                break;
              case 0xC: // Program Change
                if (isset($programTable[$channel])) {
                    $programTable[$channel] []= $chunk['ProgramNumber'];
                } else {
                    $programTable[$channel] = array($chunk['ProgramNumber']);
                }
                break;
            }
        }
    }
    $channelList  = array_keys($channelTable);
    sort($channelList);
    echo "    Channel: ".implode(' ', $channelList).PHP_EOL;
    foreach ($channelList as $channel) {
        $programs = $programTable[$channel];
        echo "      Channel[$channel]:".PHP_EOL;
        echo "        Program: ".implode(' ', $programs).PHP_EOL;
        echo "        NoteOn: ".$noteOnTable[$channel];
        echo "  NoteOff: ".$noteOnTable[$channel];
        $noteKeyList = array_keys($noteKeyTable[$channel]);
        echo "  KeyRange: ".MIN($noteKeyList)."-".MAX($noteKeyList);
        echo PHP_EOL;
    }
}



