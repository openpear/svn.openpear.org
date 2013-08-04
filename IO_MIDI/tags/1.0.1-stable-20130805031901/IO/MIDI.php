<?php
  /*
   http://www.omnibase.net/smf/
   http://www.sonicspot.com/guide/midifiles.html
   */

require_once 'IO/Bit.php';

class IO_MIDI {
    var $header = null;
//    var $track_list = array();
    var $tracks = array();
    var $_mididata = null;
    
    function parse($mididata) {
        $this->_mididata = $mididata;
        $reader = new IO_Bit();
        $reader->input($mididata);
        while ($reader->hasNextData(4)) {
            $chunk = $this->_parseChunk($reader);
            if (isset($chunk['header'])) {
                $this->header = $chunk;
            } elseif(isset($chunk['track'])) {
                $this->tracks []= $chunk;
            }
        }
    }
    function _parseChunk(&$reader) {
        list($offset, $dummy) = $reader->getOffset();
        $type = $reader->getData(4);
        $length = $reader->getUI32BE();
        $nextOffset = $offset + 8 + $length;
        $chunk = array ('type' => $type, 'length' => $length, '_offset' => $offset);
        switch ($type) {
          case 'MThd':
              $chunk['header'] = $this->_parseChunkHeader($reader);
              break;
          case 'MTrk':
              $chunk['track'] = $this->_parseChunkTrack($reader, $nextOffset);
              break;
          default:
              throw new Exception("Unknown chunk (type=$type)\n");
        }
        list($doneOffset, $dummy) = $reader->getOffset();
        if ($doneOffset !== $nextOffset) {
            echo "done:$doneOffset next:$nextOffset".PHP_EOL;
        }
        $reader->setOffset($nextOffset, 0);
        return $chunk;
    }

    function _parseChunkHeader($reader) {
        $header = array();
        $header['Format'] = $reader->getUI16BE();
        $header['NumberOfTracks'] = $reader->getUI16BE();
        $division = $reader->getUI16BE();
        $header['DivisionFlag'] = $division >> 15;
        $header['Division'] = $division & 0x7fff;
        return $header;
    }

    function _parseChunkTrack($reader, $nextOffset) {
        $track = array();
        $prev_status = null;
        while (true) {
            list($offset, $dummy) = $reader->getOffset();
            if ($offset >= $nextOffset) {
                break; // done
            }
            $chunk = array('_offset' => $offset);
            // delta time
            $chunk['DeltaTime'] = $this->getVaribleLengthValue($reader);
            // event
            $status = $reader->getUI8(); // status byte
            while ($status < 0x80) { // running status
                $status = $prev_status;
                $reader->incrementOffset(-1, 0); // 1 byte back
            }
            $eventType = $status >> 4;
            $midiChannel = $status & 0x0f;
            $chunk['EventType'] = $eventType;
            $chunk['MIDIChannel'] = $midiChannel;
            switch ($eventType) {
              case 0x8: // Note Off
              case 0x9: // Note On
                $chunk['NoteNumber'] = $reader->getUI8();
                $chunk['Velocity'] = $reader->getUI8();
                break;
              case 0xA: // Note Aftertouch Event
                $chunk['NoteNumber'] = $reader->getUI8();
                $chunk['Amount'] = $reader->getUI8();
                break;
              case 0xB: // Controller
                $chunk['ControllerType'] = $reader->getUI8();
                $chunk['Value'] = $reader->getUI8();
                break;
              case 0xC: // Program Change
                $chunk['ProgramNumber'] =  $reader->getUI8();
                break;
              case 0xD: // Note Aftertouch Event
                $chunk['Amount'] = $reader->getUI8();
                break;
              case 0xE: // Pitch Bend Event
                $value =  $reader->getUI8();
                $value = (($reader->getUI8() & 0x7f) << 7) + ($value & 0x7f);
                $chunk['Value'] = $value - 0x2000;
                break;
              case 0xF: // Meta Event of System Ex
                unset($chunk['MIDIChannel']);
                if ($midiChannel == 0xF) { // not midiChannel
                    $metaEventType = $reader->getUI8();
                    $chunk['MetaEventType'] = $metaEventType;
                    $length = $this->getVaribleLengthValue($reader);
                    $chunk['MetaEventData'] = $reader->getData($length);
                    break;
                } else if ($midiChannel == 0x0) { // System Ex
                    $length = $this->getVaribleLengthValue($reader);
                    $chunk['SystemEx'] = $reader->getData($length);
                    break;
                } else {
                    printf("unknown status=0x%02X\n", $status);
                }
              default:
                printf("unknown EventType=0x%02X\n", $eventType);
                var_dump($chunks);
                exit (0);
            }
            list($offset2, $dummy) = $reader->getOffset();
            $chunk['_length'] = $offset2 - $offset;
            $track[] = $chunk;
            $prev_status = $status;
        }
        return $track;
    }
    
    function getVaribleLengthValue($reader) {
        $ret_value = 0;
        while (true) {
            $value = $reader->getUI8();
            if ($value & 0x80) {
                $ret_value = ($ret_value << 7) + ($value & 0x7f);
            } else {
                $ret_value = ($ret_value << 7) + $value;
                break;
            }
        }
        return $ret_value;
    }

    var $event_name = array(
        0x8 => 'Note Off',
        0x9 => 'Note On',
        0xA => 'Note Aftertouch Eventn',
        0xB => 'Controller',
        0xC => 'Program Change',
        0xD => 'Note Aftertouch Event',
        0xE => 'Pitch Bend Event',
        0xF => 'Meta Event',
        );
    var $meta_event_name = array(
        0x00 => 'Sequence Number',
        0x01 => 'Text',
        0x02 => 'Copyright Notice',
        0x03 => 'Sequence/Track Name',
        0x04 => 'Instrument Name',
        0x05 => 'Lylic',
        0x06 => 'Marker',
        0x07 => 'Cue Point',
        0x20 => 'MIDI Channel Prefix',
        0x2F => 'End of Track',
        0x51 => 'Set Tempo',
        0x54 => 'SMPTE Offset',
        0x58 => 'Time Signature',
        0x59 => 'Key Signature',
        0x7F => 'Sequencer Specific',
        );
    var $controller_type_name = array(
// http://www.bass.radio42.com/help/html/f7f8b18f-a4a4-91bf-83c1-651b8dfc8f96.htm
        0 => 'BankSelect',
        1 => 'Modulation',
        2 => 'BreathControl',
        3 => 'User3',
        4 => 'FootControl',
        5 => 'PortamentoTime',
        6 => 'DataEntry',
        7 => 'MainVolume',
        8 => 'Balance',
        9 => 'User9',
        10 => 'Panorama',
        11 => 'Expression',
        12 => 'EffectControl1', 13 => 'EffectControl2',
        14 => 'User14', 15 => 'User15',
        16 => 'GeneralPurpose1', 17 => 'GeneralPurpose2',
        18 => 'GeneralPurpose3', 19 => 'GeneralPurpose4',
        20 => 'User20', 21 => 'User21', 22 => 'User22', 23 => 'User23',
        24 => 'User24', 25 => 'User25', 26 => 'User26', 27 => 'User27',
        28 => 'User28', 29 => 'User29', 30 => 'User30', 31 => 'User31',
        32 => 'BankSelectFine',
        33 => 'ModulationFine',
        34 => 'BreathControlFine',
        35 => 'User3Fine',
        36 => 'FootControlFine',
        37 => 'PortamentTimeFine',
        38 => 'DataEntryFine',
        39 => 'MainVolumeFine',
        40 => 'BalanceFine',
        41 => 'User9Fine',
        42 => 'PanoramaFine',
        43 => 'ExpressionFine',
        44 => 'EffectControl1Fine', 45 => 'EffectControl2Fine',
        46 => 'User14Fine', 47 => 'User15Fine',
        48 => 'GeneralPurpose1Fine', 49 => 'GeneralPurpose2Fine',
        50 => 'GeneralPurpose3Fine', 51 => 'GeneralPurpose4Fine',
        52 => 'User20Fine', 53 => 'User21Fine', 54 => 'User22Fine',
        55 => 'User23Fine', 56 => 'User24Fine', 57 => 'User25Fine',
        58 => 'User26Fine', 59 => 'User27Fine', 60 => 'User28Fine',
        61 => 'User29Fine', 62 => 'User30Fine', 63 => 'User31Fine',
        64 => 'HoldPedal1',
        65 => 'Portamento',
        66 => 'SutenutoPedal',
        67 => 'SoftPedal',
        68 => 'LegatoPedal',
        69 => 'HoldPedal2',
        70 => 'SoundVariation',
        71 => 'SoundTimbre',
        72 => 'SoundReleaseTime',
        73 => 'SoundAttackTime',
        74 => 'SoundBrightness',
        75 => 'SoundControl6', 76 => 'SoundControl7', 77 => 'SoundControl8',
        78 => 'SoundControl9',79 => 'SoundControl10',
        80 => 'GeneralPurposeButton1', 81 => 'GeneralPurposeButton2',
        82 => 'GeneralPurposeButton3', 83 => 'GeneralPurposeButton4',
        84 => 'GeneralPurposeButton5', 85 => 'GeneralPurposeButton6',
        86 => 'GeneralPurposeButton7', 87 => 'GeneralPurposeButton8',
        88 => 'GeneralPurposeButton9', 89 => 'GeneralPurposeButton10',
        90 => 'GeneralPurposeButton11',
        91 => 'EffectsLevel',
        92 => 'TremeloLevel',
        93 => 'ChrusLevel',
        94 => 'CelesteLevel',
        95 => 'PhaserLevel',
        96 => 'DataButtonIncrement', 97 => 'DataButtonDecrement',
        98 => 'NonRegisteredParameterFine',
        99 => 'NonRegisteredParameterCoarse',
        100 => 'RegisteredParameterFine',
        101 => 'RegisteredParameterCoarse',
        102 => 'User102', 103 => 'User103', 104 => 'User104',
        105 => 'User105', 106 => 'User106', 107 => 'User107',
        108 => 'User108', 109 => 'User109', 110 => 'User110',
        111 => 'User111', 112 => 'User112', 113 => 'User113',
        114 => 'User114', 115 => 'User115', 116 => 'User116',
        117 => 'User117', 118 => 'User118', 119 => 'User119',
        120 => 'AllSoundOff',
        121 => 'AllControllerReset',
        122 => 'LocalKeyboard',
        123 => 'AllNotesOff',
        124 => 'OmniModeOff', 125 => 'OmniModeOn',
        126 => 'MonoOperation', 127 => 'PolyOperation',
        );
    function dump($opts = array()) {
        if (empty($opts['hexdump']) === false) {
            $bitio = new IO_Bit();
            $bitio->input($this->_mididata);
        }
        echo "HEADER:\n";
        foreach ($this->header['header'] as $key => $value) {
            echo "  $key: $value\n";
        }
        if (empty($opts['hexdump']) === false) {
            $bitio->hexdump(0, $this->header['length'] + 8);
        }
        foreach ($this->tracks as $idx => $track) {
            echo "TRACK[$idx]:\n";
            if (empty($opts['hexdump']) === false) {
                $bitio->hexdump($track['_offset'], 8);
            }
            foreach ($track['track'] as $idx2 => $chunk) {
                echo "  [$idx2]:";
                foreach ($chunk as $key => $value) {
                    switch ($key) {
                      case 'EventType':
                        $eventname = $this->event_name[$value];
                        echo " $key:$value($eventname),";
                        break;
                      case 'MetaEventType':
                        if (isset($this->meta_event_name[$value])) {
                            $eventname = $this->meta_event_name[$value];
                            echo " $key:$value($eventname),";
                        } else {
                            echo " $key:$value,";
                        }
                        break;
                      case 'ControllerType':
                        $typename = $this->controller_type_name[$value];
                        echo " $key:$value($typename),";
                        break;
                      default:
                        echo " $key:$value,";
                        break;
                    }
                }
                echo "\n";
                if (empty($opts['hexdump']) === false) {
                    $bitio->hexdump($chunk['_offset'], $chunk['_length']);
                }
            }
        }

    }
    function build() {
        ;
    }
}
