<?php
  /*
   http://www.omnibase.net/smf/
   http://sfkonu.vni.jp/sbcsoft/mgame/mldtest.html
   */

require_once 'IO/Bit.php';

class IO_SMAF {
    var $headers = array();
    var $chunks = array();
    
    var $nestedChunkIDList = array(
        // ChunkID => (Child) Data Structure Type
//        'MMMD' => 'Chunks', // Mobile Application Data Chunk
        'OPDA' => 'Chunks', // Optional Data Chunk
        'Dch' => 'Tags',
        'MTR' => 'Chunks',
        );
    var $trackChunkIDList = array(
        'MTR', 'ATR', 'GTR'
        );
    function nestedChunkIDSize($chunkID) {
        foreach ($this->nestedChunkIDList as $id => $size) {
            if ($id === substr($chunkID, 0, strlen($id))) {
                return $size;
            }
        }
        return 0;
    }
    function parse($smafdata) {
        $reader = new IO_Bit();
        $reader->input($smafdata);
        $this->headers['ID'] = $reader->getData(4);
        $size = $reader->getUI32BE(4);
        $this->headers['Size'] = $size;
        while ($reader->hasNextData(8)) {
            $this->chunks = $this->parseDataChunks($reader);
        }
    }
    function seekToAlphabet(&$reader) {
        while (true) {
            $data = $reader->getData(1);
            if (ctype_alpha($data)) {
                break;
            }
        }
        $reader->incrementOffset(-1, 0);
    }

    function parseDataChunks(&$reader) {
        $chunks = array();
        while ($reader->hasNextData(8)) {
            $chunk = array();
            $chunkID = $reader->getData(4);
            $size = $reader->getUI32BE();
            $chunk['ID'] = $chunkID;
            $chunkID_3byte = substr($chunkID, 0, 3);
            $chunkID_4th_value = ord($chunkID{3});
            switch ($chunkID_3byte) {
            case 'Dch':
                $chunk['ID'] = $chunkID_3byte;
                $chunk['CodeType'] = $chunkID_4th_value;
                break;
            case 'MTR':
            case 'ATR':
            case 'GTR':
                $chunk['ID'] = $chunkID_3byte;
                $chunk['TrackNumber'] = $chunkID_4th_value;
                break;
            }
            $chunk['Size'] = $size;
            switch (substr($chunkID, 0, 3)) {
              case 'CNT': // CNTI: Contents Information Chunk
                $chunk['Contents Class'] = $reader->getUI8();
                $chunk['Contents Type'] = $reader->getUI8();
                $chunk['Contents Code Type'] = $reader->getUI8();
                $chunk['Copy Status'] = $reader->getUI8();
                $chunk['Copy Counts'] = $reader->getUI8();
                if ($size > 5) {
                    $chunk['Option'] = $reader->getData($size - 5);
                }
                break;
            case 'MTR': // MTR*: Score Track Chunk
                $chunk['Format Type'] = $reader->getUI8();
                $chunk['Sequence Type'] = $reader->getUI8();
                $chunk['Timebase D'] = $reader->getUI8();
                $chunk['Timebase G'] = $reader->getUI8();
                $chunk['Channnel Status'] = $reader->getUI16BE();
                $data = $reader->getData($size - 6);
                $reader_chunkdata = new IO_Bit();
                $reader_chunkdata->input($data);
                $this->seekToAlphabet($reader_chunkdata);
                $chunk[$structure_type] = $this->parseDataChunks($reader_chunkdata);
                break;
            case 'Mts': // Mtsu, Mtsq, Mts
                $data = $reader->getData($size);
                $reader_tracks = new IO_Bit();
                $reader_tracks->input($data);
                switch ($chunkID) {
                case 'Mtsu': // Score Track Setup Data
                case 'Mtsq': // Score Track Sequence Data
                    $tracks = array();
                    while ($reader_tracks->hasNextData(4)) {
                        $end_check = $reader_tracks->getUI32BE();
                        if ($end_check == 0) {
                            break;
                        }
                        $reader_tracks->incrementOffset(-4, 0);
                        $track = array();

                        // Duration
                        if ($chunkID != 'Mtsu') { // Mtsu は Duration 無し
                            $duration = $reader_tracks->getUI8();
                            if ($duration & 0x80) {
                                $duration = ($duration & 0x7F) << 8;
                                $duration |= $reader_tracks->getUI8();
                            }
                            $track['Duration'] = $duration;
                        }
                        // Event
                        $event = $reader_tracks->getUI8();
                        $eventData = array();
                        if ($event != 0) {
                            $eventData['Type'] = 'Note Message';
                            $eventData['GetTime'] = $reader_tracks->getUI8();
                            // Option ???
                        } else {
                            $data1 = $reader_tracks->getUI8();
                            $eventData['Channel'] = $data1 >> 6;
                            if (($data1 & 0x0f) == 0) {
                                $eventData['Type'] = 'Program change';
                            } elseif (($data1 & 0x0f) == 1) {
                                $eventData['Type'] = 'Bank Select';
                            } else {
                                $msg = sprintf("Unknow Event Data#1=0x%02x", $data1);
//                                throw new Exception($msg);
                                printf($msg."\n");
                                break 2;
                            }
                            $eventData['Value'] = $reader_tracks->getUI8();
                        }
                        $track['event'] = 
                        $tracks []= $track;
                    }
                    $chunk['Tracks'] = $tracks;
                    break 2;
                }
              default:
                $data = $reader->getData($size);
                if ($structure_type = $this->nestedChunkIDSize($chunkID)) {
                    $reader_chunkdata = new IO_Bit();
                    $reader_chunkdata->input($data);
                    $method = 'parseData'.$structure_type;
                    $chunk[$structure_type] = $this->$method($reader_chunkdata);
                } else {
                    $chunk['Data'] = $data;
                }
                break;
            }
//            var_dump($chunk);
            $chunks []= $chunk;
        }
        return $chunks;
    }
    function parseDataTags(&$reader) {
        $tags = array();
        while ($reader->hasNextData(4)) {
            $tag = array();
            $tagID = $reader->getData(2);
            $size = $reader->getUI16BE();
            $tag['ID'] = $tagID;
            $tag['Size'] = $size;
            $tag['Data'] = $reader->getData($size);
//            var_dump($tag);
            $tags []= $tag;
        }
        return $tags;
    }
    function dump() {
        echo "Header:\n";
        echo "  ID:".$this->headers['ID']."\n";
        echo "  Size:".$this->headers['Size']."\n";
        echo "Body:\n";
        $this->dumpChunks($this->chunks, 1);
    }
    function dumpChunks($chunks, $indent) {
        $indent_space = str_pad('', 2*$indent, ' ');
        foreach ($chunks as $chunk) {
            foreach ($chunk as $key => $value) {
                if ($key === 'Chunks') {
                    echo "$indent_space$key:\n";
                    $this->dumpChunks($value, $indent + 1);
                } elseif  ($key === 'Tags') {
                    echo "$indent_space$key:\n";
                    $this->dumpTags($value, $indent + 1);
                } elseif  ($key === 'Tracks') {
                    echo "$indent_space$key:\n";
                    $this->dumpTracks($value, $indent + 1);
                } elseif (is_array($value) === false) {
                    echo "$indent_space$key:$value\n";
                } else {
                    echo "$indent_space$key:\n";
                    var_dump($value);
                }
            }
        }
    }
    function dumpTags($tags, $indent) {
        $indent_space = str_pad('', 2*$indent, ' ');
        foreach ($tags as $tag) {
            echo $indent_space;
            foreach ($tag as $key => $value) {
                echo "$key:$value ";
            }
            echo "\n";
        }
    }
    function dumpTracks($tracks, $indent) {
        $indent_space = str_pad('', 2*$indent, ' ');
        foreach ($tracks as $track) {
            echo $indent_space;
            foreach ($track as $key => $value) {
                echo "$key:$value ";
            }
            echo "\n";
        }
    }
}
