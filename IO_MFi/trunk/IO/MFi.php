<?php
  /*
   http://www.omnibase.net/smf/
   http://sfkonu.vni.jp/sbcsoft/mgame/mldtest.html
   */

require_once 'IO/Bit.php';

class IO_MFi {
    var $headers = array();
    var $datainfos = array();
    var $tracks = array(); // or into datainfo ? what?
    
    function parse($mfidata) {
        $reader = new IO_Bit();
        $reader->input($mfidata);
        // Header Part
        $identifier = $reader->getData(4);
        if ($identifier != 'melo') {
            throw new Exception('Identifer($identifier) is not melo');
        }
        $this->headers['Identifier'] = $identifier;
        $fileLength = $reader->getUI32BE();
        $realFileLength = strlen($mfidata);
        if ($fileLength != $realFileLength - 8) {
            throw new Exception("FileLength($fileLength) is not real FileLength($realFileLength) - 8");
        }
        $this->headers['FileLength'] = $fileLength;
        $offsetToTrack = $reader->getUI16BE();
        $this->headers['OffsetToTrack'] = $offsetToTrack;
        $this->headers['DataTypeMajor'] = $reader->getUI8();
        $this->headers['DataTypeMinor'] = $reader->getUI8();
        $this->headers['NumberOfTrack'] = $reader->getUI8();
        // Data Information Part
        $reader_di = new IO_Bit();
        $reader_di->input($reader->getData($offsetToTrack - 3));
        $note_message_length_info = 0;
        while ($reader_di->hasNextData(6)) {
            $info = array();
            $identifer = $reader_di->getData(4);
            $length = $reader_di->getUI16BE();
            $info['Identifer'] = $identifer;
            $info['Length'] = $length;
            switch($identifer) {
            case 'titl':
            case 'copy':
            case 'prot':
            case 'date':
                $data = $reader_di->getData($length);
                break;
            case 'sorc':
                $data = $reader_di->getUI8($length);
                break;
            case 'note':
            case 'exst':
                $data = $reader_di->getUI16BE($length);
                if ($identifer == 'note') {
                    $note_message_length_info = $data;
                }
                break;
            case 'vers':
                $data = $reader_di->getUI32BE($length);
                break;
            }
            $info['Data'] = $data;
            $this->datainfos []= $info;
        }
        // Track Part
        while ($reader->hasNextData(8)) {
            $track = array();
            $track['Identifer'] = $reader->getData(4);
            $length = $reader->getUI32BE();
            $track['Length'] = $length;
            $reader_ev = new IO_Bit();
            $reader_ev->input($reader->getData($length));
            $track['Events'] = array();
            while ($reader_ev->hasNextData(3)) {
                $event = array();
                $event['DeltaTime'] = $reader_ev->getUI8();
                $statusInfo = $reader_ev->getUI8();
                $event['StatusInfo'] = $statusInfo;
                switch ($statusInfo) {
                  default:
                    $event['Data1'] = $reader_ev->getUI8();
                    if ( $note_message_length_info == 1) { // MFi2
                        $event['Data2'] = $reader_ev->getUI8();
                    }
                    break;
                  case 0xFF:
                    $statusInfo2 = $reader_ev->getUI8();
                    $event['StatusInfo2'] = $statusInfo2;
                    switch($statusInfo2) {
                      default;
                        $event['Data'] = $reader_ev->getUI8();
                        break;
                      case 0xF0:
                      case 0xF1:
                      case 0xFF:
                        $length = $reader_ev->getUI16BE();
                        $event['Length'] = $length;
                        $event['Data'] = $reader_ev->getData($length);
                        break;
                    }
                }
                $track['Events'][] = $event;
            }
            $this->tracks []= $track;
        }
    }
    function dump() {
        echo "Header Part:\n";
        foreach ($this->headers as $key => $value) {
            echo "  $key: $value\n";
        }
        echo "Data Information Part:\n";
        foreach ($this->datainfos as $idx => $datainfo) {
            echo "  [$idx]\n";
            foreach ($datainfo as $key => $value) {
                echo "    $key: $value\n";
            }
        }
        echo "Track Part\n";
        foreach ($this->tracks as $track_idx => $track) {
            echo "  [$track_idx]\n";
            foreach ($track as $key => $name) {
                if ($key != 'Events') {
                    echo "    $key: $value\n";
                } else {
                    echo "    Event:\n";
                    foreach ($track[$key] as $event_idx => $event) {
                        echo "      [$event_idx]\n";
                        foreach ($event as $key2 => $value2) {
                            echo "        $key2: $value2\n";
                        }
                    }
                }
            }
        }
    }
}
