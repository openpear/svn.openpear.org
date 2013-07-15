<?php
  /*
   http://www.omnibase.net/smf/
   http://www.sonicspot.com/guide/midifiles.html
   */

require_once 'IO/Bit.php';

class IO_MIDI {
    var $header = null;
    var $track_list = array();
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
                $this->track []= $chunk;
            }
        }
    }
    function _parseChunk(&$reader) {
        list($offset, $dummy) = $reader->getOffset();
        $type = $reader->getData(4);
        $length = $reader->getUI32BE();
        $chunk = array ('type' => $type, 'length' => $length, '_offset' => $offset);
        switch ($type) {
          case 'MThd':
              $chunk['header'] = $this->_parseChunkHeader($reader);
              break;
          case 'MTrk':
              $chunk['track'] = $this->_parseChunkTrack($reader);
              break;
          default:
              throw new Exception("Unknown chunk (type=$type)\n");
        }
        list($doneOffset, $dummy) = $reader->getOffset();
        $nextOffset = $offset + 8 + $length;
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

    function _parseChunkTrack($reader) {
        $track = array();
        $prev_status = null;
        while (true) {
            list($offset, $dummy) = $reader->getOffset();
            if ($reader->hasNextData(3) === false) { // XXX 3? or 4
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
                $value = ($value << 7) & $reader->getUI8();
                $chunk['Value'] = $value;
                break;
              case 0xF: // Meta Event
                $metaEventType = $reader->getUI8();
                $chunk['MetaEventType'] = $metaEventType;
                $length = $this->getVaribleLengthValue($reader);
                $chunk['MetaEventData'] = $reader->getData($length);
                break;
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
        foreach ($this->track as $idx => $track) {
            echo "TRACK[$idx]:\n";
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
}
