<?php

require_once('IO/Bit.php');

class IO_Zlib_HuffmanDecompress {
    
}

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
                        if ($value < 9) {
                            $length_extend_bits = 0;
                        } else {
                            $length_extend_bits = floor(($value - 5) / 4);
                        }
                        $length_extend = $reader->getUIBitsLSB($length_extend_bits);
                        //
                        $distance_value = $reader->getUIBitsLSB(5);
                        $distance = self::$distance_table[$distance_value];
                        if ($distance_value < 4) {
                            $distance_extend_bits = 0;
                        } else {
                            $distance_extend_bits = floor(($distance_value - 3) / 2);
                        }
                        $distance_extend = $reader->getUIBitsLSB($distance_extend_bits);
                        $data []= array('Value' => $value,
                                        'Length' => $length,
                                        'LengthExtend' => $length_extend,
                                        'Value2' => $distance_value,
                                        'Distance' => $distance,
                                        'DistanceExtend' => $distance_extend);

                    } else {
                        $value = ($value << 1) | $reader->getUIBitLSB();
                        if ($value <= 0xBF) {
                            $real_value = $value - 0x30;
                            $data []= array('Value' => $value,
                                            'RealValue' => $real_value);
                        } else if ($value <= 0xC7) {
                            $length = self::$length_table2[$value - 0xC0];
                            if ($value ==  0xC0) {
                                $extend_bits = 4;
                            } elseif ($value <  0xC0 + 5) {
                                $extend_bits = 5;
                            } else {
                                $extend_bits = 0;
                            }
                            $length_extend_value = $reader->getUIBitsLSB($extend_bits);
                            //
                            $distance_value = $reader->getUIBitsLSB(5);
                            $distance = self::$distance_table[$distance_value];
                            if ($value2 < 4) {
                                $distance_extend_bits = 0;
                            } else {
                                $distance_extend_bits = floor(($distance_value - 3) / 2);
                            }
                            $distance_extend = $reader->getUIBitsLSB($distance_extend_bits);
                            $data []= array('LengthValue' => $value,
                                            'Length' => $length,
                                            'LengthExtend' => $length_extend,
                                            'DistanceValue' => $distance_value,
                                            'Distance' => $distance,
                                            'DistanceExtend' => $distance_extend);
                        } else {
                            $value = ($value << 1) | $reader->getUIBitLSB();
                            $real_value = $value - 0x190 + 144;
                            $data []= array('Value' => $value,
                                            'RealValue' => $real_value);
                        }
                    }
                } // while end
                $block['Data'] = $data;
                break;
            case 2: // compressed with dynamic Huffman codes
                $hlit = $reader->getUIBitsLSB(5);
                $hdist  = $reader->getUIBitsLSB(5);
                $hclen  = $reader->getUIBitsLSB(4);
                $block['HLIT'] = $hlit;
                $block['HDIST'] = $hdist;
                $block['HCLEN'] = $hclen;
                $hclen_order = array(16, 17, 18, 0, 8,
                                     7, 9,
                                     6, 10,
                                     5, 11,
                                     4, 12,
                                     3, 13,
                                     2, 14,
                                     1, 15);
                $hclen_table = array_fill(0, 19, 0);
                for ($i = 0 ; $i < $hclen + 4; $i++) {
                    $hclen_table[$hclen_order[$i]] = $reader->getUIBitsLSB(3);
                }
                for ( ; $i < 18 ; $i++) {
                    $hclen_table[$hclen_order[$i]] = 0;
                }
                $block['HCLEN_TABLE'] = $hclen_table;
                // GENERATE HUFFMAN CODE TABLE FROM HCLEN TABLE
                $hclen_min = 15;
                $hclen_max = 0;
                for ($i = 0 ; $i < 19; $i++) {
                    $value = $hclen_table[$i];
                    if ($value != 0) {
                        if ($value < $hclen_min) {
                            $hclen_min = $value;
                        }
                        if ($value > $hclen_max) {
                            $hclen_max = $value;
                        }
                    }
                }
                $hclen_lists = array_fill($hclen_min, $hclen_max, array());
                echo "hclen_min:$hclen_min hclen_max:$hclen_max\n";
                $hccode_table = array_fill(0, 19, null);
                $hccode_table_rev = array();
                $value = 0;
                for ($i = $hclen_min ; $i <= $hclen_max ; $i++) {
                    $hccode_table_rev[$i] = array();
                    for ($j = 0 ; $j < 19; $j++) {
                        if ($hclen_table[$j] == $i) {
                            $hccode_table[$j] = $value;
                            $hccode_table_rev[$i][$value] = $j;
                            $value ++;
                        }
                    }
                    $value *= 2;
                }
                $block['HCCODE_TABLE'] = $hccode_table;
                
                // while (true) {
                
                // } // while end
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
            $btype = $block['BTYPE'];
            echo "(BFINAL)={$block['(BFINAL)']} BTYPE:$btype\n";
            switch ($btype)  {
            case 0:
                echo "LEN:{$block['LEN']} NLEN:{$block['NLEN']} Data:{$block['Data']}\n";
                break;
            case 1:
                foreach ($block['Data'] as $value) {
                    if (isset($value['RealValue'])) {
                        printf("%02X=>%02X(%c) ", $value['Value'], $value['RealValue'], $value['RealValue']);
                    } else if (isset($value['Length'])) {
                        echo "Length:{$value['Length']}+{$value['LengthExtend']} Distance:{$value['Distance']}+{$value['DistanceExtend']} ";
                    } else { // Maybe Terminate Value
                        printf("%d(Terminate)", $value['Value']);
                    }
                }
                echo "\n";
            break;
            case 2:
                print_r($block);
                break;
            }
        }
        echo "ADLER32:{$this->adler32}\n";
    }
}
