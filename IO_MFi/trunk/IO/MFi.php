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
    var $noteTable = Array(
        'A', 'A+', 'B', 'C', 'C+', 'D', 'D+', 'E', 'F', 'F+', 'G', 'G+',
        'A', 'A+', 'B', 'C', 'C+', 'D', 'D+', 'E', 'F', 'F+', 'G', 'G+',
        'A', 'A+', 'B', 'C', 'C+', 'D', 'D+', 'E', 'F', 'F+', 'G', 'G+',
        'A', 'A+', 'B', 'C', 'C+', 'D', 'D+', 'E', 'F', 'F+', 'G', 'G+',
        'A', 'A+', 'B', 'C', 'C+', 'D', 'D+', 'E', 'F', 'F+', 'G', 'G+',
        'A', 'A+', 'n/a'
        );
    
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
                    $event['Data'] = $reader_ev->getUI8();
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
            echo "  $key: $value";
            switch ($key) {
              case 'DataTypeMajor':
                switch ($value) {
                  case 1:
                    echo " (melo data)";
                    break;
                  case 2:
                    echo " (music data MFi2)";
                    break;
                }
                break;
              case 'DataTypeMinor':
                switch ($value) {
                  case 1:
                    echo " (music data)";
                    break;
                  case 2:
                    echo " (whole)";
                    break;
                  case 3:
                    echo " (partial)";
                    break;
                }
                break;
            }
            echo "\n";
        }
        echo "Data Information Part:\n";
        foreach ($this->datainfos as $idx => $datainfo) {
            echo "  [$idx] ".$datainfo['Identifer']." (len:".$datainfo['Length'].") Data: ".$datainfo['Data']."\n";
        }
        echo "Track Part\n";
        foreach ($this->tracks as $track_idx => $track) {
            echo "  [$track_idx]\n";
            foreach ($track as $key => $value) {
                if ($key !== 'Events') {
                    echo "    $key: $value\n";
                } else {
                    echo "    Event:\n";
                    foreach ($track[$key] as $event_idx => $event) {
                        echo "      [$event_idx]";
                        echo "  DeltaTime: ".$event['DeltaTime'];
                        echo "  StatusInfo: ".$event['StatusInfo'];
                        if (isset($event['StatusInfo2'])) {
                            echo "  ".$event['StatusInfo2'];
                        }
                        echo "  Data: ".$event['Data'];
                        if (isset($event['Data2'])) {
                            echo " ".$event['Data2'];
                        }
                        echo "\n";
                        if ($event['StatusInfo'] < 0xF0) {
                            // Basical Status
                            $status = $event['StatusInfo'];
                            $voice = $status >> 6;
                            $note = $status & 0x3f;
                            $noteStr = $this->noteTable[$note];
                            $noteLength = $event['Data'];
                            echo "             Voice: $voice  Note: $note($noteStr)  Length:$noteLength\n";
                            if (isset($event['Data2'])) {
                                $data2 = $event['Data2'];
                                $velocity = $data2 >> 2;
                                $octaveShift = $data2 & 0x3;
                                echo "             Velocity: $velocity  OctaveShift: $octaveShift\n";
                            }
                        } else {
                            // Extended Status
                        }
                    }
                }
            }
        }
    }
}
