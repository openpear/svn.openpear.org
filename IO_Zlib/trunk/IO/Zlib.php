<?php

require_once('IO/Bit.php');

class IO_Zlib {
    var $cmf;
    var $flg;
    var $dictid = null;
    var $compressed_data = null;
    static $length_table1 = array(
        3, 4, 5, 6, 7, 8, 9, 10, 11, 13,
        15, 17, 19, 23, 27, 31, 35, 43, 51, 59,
        67, 83, 99
        );
    static $length_table2 = array(
        115, 131, 163, 195, 227, 258
        );
    static $distance_table = array(
        1, 2, 3, 4, 5, 7, 9, 13 ,17 ,25,
        33, 49, 65, 97, 129, 193, 257, 385, 513, 769,
        1025, 1537, 2049, 3073, 4097, 6145, 8193, 12289, 16385, 24577
        );
    function parse($zipdata) {
        $reader = new IO_Bit();
        $reader->input($zipdata);
        /*
         * zlib header info
         */
        $this->cmf = $reader->getUI8();
        $cm = $this->cmf & 0x0f;
        if ($cm != 8) { // CM
            new Exception("unknown compression method=$cm");
        }
        $this->flg = $reader->getUI8();
        if ($this->flg & 0x20) {
            $this->dictid = $reader->getUI32BE();
        }
        /*
         * compression data block
         */
        $compressd_data = array();
        while (true) {
            $block = array();
            $bfinal = $reader->getUIBitLSB();
            $btype = $reader->getUIBitsLSB(2);
            $block['(BFINAL)'] = $bfinal;
            $block['BTYPE'] = $btype;
            switch ($btype) {
            case 0: // no compression data
                $len = $reader->getUI16LE();
                $block['LEN'] = $len;
                $block['NLEN'] = $reader->getUI16LE(); // 1's complement of LEN
                $block['Data'] = $reader->getData($len);
                break;
            case 1: // compressed with fixed Huffman codes;
                $data = array();
                while (true) {
                    $value = 0;
                    for ($i = 0 ; $i < 7 ; $i++) {
                        $value = ($value << 1) | $reader->getUIBitLSB();
                    }
                    if ($value == 0) { // End of Data
                        $data []= array('Value' => $value);
                        break;
                    } else if ($value <= 0x17) {
                        $length = self::$length_table1[$value - 1];
                        $value2 = $reader->getUIBitsLSB(5);
                        $distance = self::$distance_table[$value2];
                        $data []= array('Value' => $value,
                                        'Length' => $length,
                                        'Value2' => $value2,
                                        'Distance' => $distance);
                    } else {
                        $value = ($value << 1) | $reader->getUIBitLSB();
                        if ($value <= 0xBF) {
                            $real_value = $value - 0x30;
                            $data []= array('Value' => $value,
                                            'RealValue' => $real_value);
                        } else if ($value <= 0xC7) {
                            $length = self::$length_table2[$value - 0xC0];
                            $value2 = $reader->getUIBitsLSB(5);
                            $distance = self::$distance_table[$value2];
                            $data []= array('Value' => $value,
                                            'Length' => $length,
                                            'Value2' => $value2,
                                            'Distance' => $distance);
                        } else {
                            $value = ($value << 1) | $reader->getUIBitLSB();
                            $real_value = $value - 0x190 + 144;
                            $data []= array('Value' => $value,
                                            'RealValue' => $real_value);
                        }
                    }
//                    var_dump($data);
                } // while end
                $block['Data'] = $data;
                break;
            case 2: // compressed with dynamic Huffman codes
                throw new Exception("dynamic Huffman codes is not implemented yet. ");
                break;
            default: // = 3
                throw new Exception("Error BTYPE($btype)");
                break;
            }
            $compressed_data [] = $block;
            $this->compressed_data = $compressed_data;
            if ($bfinal == 1) { // BFINAL
                break;
            }
        }
        $this->adler32 = $reader->getUI32BE();
    }

    function dump() {
        /*
         * zlib header info
         */
        $cinfo = $this->cmf >> 4;
        $cm = $this->cmf & 0x0f;
        $flevel = $this->flg >> 6;
        $fdict= ($this->flg >> 5) & 1;
        $fcheck = $this->flg && 0x1f;
        printf("CMF:%02X(CINFO=%d CM=%d) FLG:0x%02X(FLEVEL=%d FDICT=%d FCHECK=%d)\n", $this->cmf, $cinfo, $cm, $this->flg, $flevel, $fdict, $fcheck);
        if (! is_null($this->dictid)) {
            printf("DICTID=0x%08X\n", $this->dictid);
        }
        /*
         *  compression data block
         */
        foreach ($this->compressed_data as $block) {
            foreach ($block as $key => $value) {
                if (! is_array($value)) {
                    echo "$key:$value ";
                } else {
                    foreach ($value as $data) {
                        echo "\n";
                        echo "\t";
                        foreach ($data as $k => $v) {
                            echo "$k:$v ";
                        }
                    }
                }
            }
            echo "\n";
        }
        echo "ADLER32:{$this->adler32}\n";
    }
}
