<?php
  /*
   http://www.omnibase.net/smf/
   http://www.sonicspot.com/guide/midifiles.html
   */

require_once 'IO/Bit.php';

class IO_MIDI {
    var $header = null;
    var $track_list = array();
    
    function parse($mididata) {
        $reader = new IO_Bit();
        $reader->input($mididata);
        while ($reader->hasNextData(4)) {
            list($byte_offset, $dummy) = $reader->getOffset();
            $chunk = $this->_parseChunk($reader);
            $chunk['_byte_offset'] = $byte_offset;
            if (isset($chunk['header'])) {
                $this->header = $chunk;
            } elseif(isset($chunk['track'])) {
                $this->track_list []= $chunk;
            }
        }
    }
    function _parseChunk(&$reader) {
        $type = $reader->getData(4);
        $length = $reader->getUI32BE();
        $data = $reader->getData($length);
        $chunk = array ('type' => $type, 'length' => $length);
        switch ($type) {
          case 'MThd':
              $chunk['header'] = $this->_parseChunkHeader($data);
              break;
          case 'MTrk':
              $chunk['track'] = $this->_parseChunkTrack($data);
              break;
          default:
              throw new Exception("Unknown chunk (type=$type)\n");
        }
        return $chunk;
    }

    function _parseChunkHeader($data) {
        $header = array();
        $reader = new IO_Bit();
        $reader->input($data);
        $header['Format'] = $reader->getUI16BE();
        $header['NumberOfTracks'] = $reader->getUI16BE();
        $division = $reader->getUI16BE();
        $header['DivisionFlag'] = $division >> 15;
        $header['Division'] = $division & 0x7fff;
        return $header;
    }

    function _parseChunkTrack($data) {
        $track = array();
        $reader = new IO_Bit();
        $reader->input($data);
        $prev_status = null;
        while ($reader->hasNextData(3)) { // XXX 3? or 4
            $chunk = array();
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
                var_dump($track);
                exit (0);
            }
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

    function dump() {
        echo "HEADER:\n";
        foreach ($this->header['header'] as $key => $value) {
            echo "  $key: $value\n";
        }
        foreach ($this->track_list as $idx => $track) {
            echo "TRACK[$idx]:\n";
            foreach ($track['track'] as $idx2 => $chunk) {
                echo "  CHUNK[$idx2]:\n";
                foreach ($chunk as $key => $value) {
                    switch ($key) {
                      case 'EventType':
                        $eventname = $this->event_name[$value];
                        echo "    $key: $value($eventname)\n";
                        break;
                      case 'MetaEventType':
                        if (isset($this->meta_event_name[$value])) {
                            $eventname = $this->meta_event_name[$value];
                            echo "    $key: $value($eventname)\n";
                        } else {
                            echo "    $key: $value\n";
                        }
                        break;
                      default:
                        echo "    $key: $value\n";
                        break;
                    }
                }
            }
        }

    }
}
