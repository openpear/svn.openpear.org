<?php

require_once('IO/Bit.php');


/*
 * BTYPE:2 Custom Huffma 用 クラス (BTYPE:1 は今の所ベタに処理)
 */

abstract class IO_Zlib_HuffmanReader {
    abstract function getValue(&$reader);
}

class IO_Zlib_HuffmanReader_Custom extends IO_Zlib_HuffmanReader {
    var $_huffman_table_rev;
    var $_hclen_min;
    var $_hclen_max;
    function __construct($hclen_list) {
        ; // ハフマン符号長からテーブルを復元して逆引きテーブルを作成する
        if (! is_array($hclen_list)) {
            throw new Exception("huffman_table_from_hclen(hclen_list=$hclen_list)");
        }
        $hclen_list_len = count($hclen_list);
        $hclen_min = min(array_diff($hclen_list, array(0)));
        $hclen_max = max($hclen_list);
        //         echo "hclen_min:$hclen_min hclen_max:$hclen_max\n";
        if ($hclen_min > $hclen_max) {
            throw new Exception("huffman_table_from_hclen: hclen_min($hclen_min) > hclen_max($hclen_max)");
        }
        $hclen_lists = array_fill($hclen_min, $hclen_max - $hclen_min, array());

        $huffman_table_rev = array();
        $value = 0;
        for ($i = $hclen_min ; $i <= $hclen_max ; $i++) {
            $huffman_table_rev[$i] = array();
            for ($j = 0 ; $j < $hclen_list_len; $j++) {
                if ($hclen_list[$j] == $i) {
                    $huffman_table_rev[$i][$value] = $j;
                    $value ++;
                }
            }
            $value *= 2;
        }
        $this->_huffman_table_rev = $huffman_table_rev;
        $huffman_table_rev_keys = array_keys($huffman_table_rev);
        $this->_hclen_min = $hclen_min;
        $this->_hclen_max = $hclen_max;
    }
    /*
     * 逆引きテーブルを参照して符号に対応する元データ(literal/length)を返す
     */
    function getValue(&$reader) {
        $value = 0;
        for ($i = 0 ; $i < $this->_hclen_min ; $i++) {
            $value = ($value << 1) | $reader->getUIBitLSB();
        }
        foreach (range($this->_hclen_min, $this->_hclen_max) as $hclen) {
            $hccode_table = $this->_huffman_table_rev[$hclen];
            if (isset($hccode_table[$value])) {
                return $hccode_table[$value];
            }
            $value = ($value << 1) | $reader->getUIBitLSB();
        }
        throw new Exception("Illegal Huffman Code");
    }
}

class IO_Zlib {
    var $cmf;
    var $flg;
    var $dictid = null;
    var $compressed_data = null;
    // fixed huffman table (length, distance)
    static $length_table = array(
        3, 4, 5, 6, 7, 8, 9, 10, 11, 13,
        15, 17, 19, 23, 27, 31, 35, 43, 51, 59,
        67, 83, 99,
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
                    // ハフマン符号をリテラル/距離符号に変換
                    for ($i = 0 ; $i < 7 ; $i++) {
                        $value = ($value << 1) | $reader->getUIBitLSB();
                    }
                    if ($value <= 0x17) { // End of Code or Length Code
                        $lit_or_len = $value + 256;
                    } else {
                        $value = ($value << 1) | $reader->getUIBitLSB();
                        if ($value <= 0xBF) { // Literal Code
                            $lit_or_len = $value - 0x30;
                        } else if ($value <= 0xC7) { // Length Code
                            $lit_or_len = $value - 0xC0 + 280;
                        } else {
                            $value = ($value << 1) | $reader->getUIBitLSB();
                            $lit_or_len = $value - 0x190 + 144;
                        }
                    }
                    // リテラル/距離符号を解釈して保存
                    if ($lit_or_len < 256) {
                        $data []= array('Value' => $lit_or_len);
                    } else if ($lit_or_len > 256) {
                        $length = self::$length_table[$lit_or_len - 257];
                        if ($value < 265) { // 256-265 => 0
                            $length_extend_bits = 0;
                        } else if ($value < 285) {
                            $length_extend_bits = floor(($lit_or_len  - 261) / 4);
                        } else { // 285 => 0
                            $length_extend_bits = 0;
                        }
                        if ($length_extend_bits == 0) {
                            $length_extend = 0;
                        } else {
                            $length_extend = $reader->getUIBitsLSB($length_extend_bits);
                        }
                        $distance_value = $reader->getUIBitsLSB(5);
                        $distance = self::$distance_table[$distance_value];

// echo "YYY: distance=$distance distance_value:$distance_value\n";
                        
                        if ($distance_value < 4) {
                            $distance_extend_bits = 0;
                        } else {
                            $distance_extend_bits = floor(($distance_value - 3) / 2);
                        }
                        if ($distance_extend_bits == 0) {
                            $distance_extend = 0;
                        } else {
                            $distance_extend = $reader->getUIBitsLSB($distance_extend_bits);
                        }
                        $data []= array('Length' => $length,
                                        'LengthExtend' => $length_extend,
                                        'Distance' => $distance,
                                        'DistanceExtend' => $distance_extend);
                    } else { // 256: End of Code
                        break;
                    }
                } // while end
                $block['Data'] = $data;
                break;
            case 2: // compressed with dynamic Huffman codes

                //  まず、ハフマン符号化されたハフマンテーブルを復元する
                $hlit  = $reader->getUIBitsLSB(5) + 257;
                $hdist = $reader->getUIBitsLSB(5) + 1;
                $hclen = $reader->getUIBitsLSB(4) + 4;
                $block['HLIT'] = $hlit;
                $block['HDIST'] = $hdist;
                $block['HCLEN'] = $hclen;
                // ハフマンテーブルのハフマン符号長を読み取る (phase 1)
                $hclen_order = array(16, 17, 18,
                                     0,  8, 7,  9, 6, 10, 5, 11,
                                     4, 12, 3, 13, 2, 14, 1, 15);
                $hclen_list = array_fill(0, 19, 0);
                for ($i = 0 ; $i < $hclen ; $i++) {
                    $value = $reader->getUIBitsLSB(3);
                    $hclen_list[$hclen_order[$i]] = $value;
                }
//                $block['HCLEN_LIST'] = $hclen_list;
                // ハフマン符号長からハフマン符号テーブルを生成する。(phase1)
                $huffman_reader_custom19 = new IO_Zlib_HuffmanReader_Custom($hclen_list);

                /*
                 * ハフマンテーブルのハフマン符号長を読み取る (phase 2)
                 */
                $lit_and_dist_count = $hlit + $hdist;
                for ($i = 0 ; $i < $lit_and_dist_count ; $i++) {
                    $lit_or_len = $huffman_reader_custom19->getValue($reader);
                    switch ($lit_or_len) {
                    default: // 1-15
                        $literal = $lit_or_len;
                        $lit_and_dist_len_list [] = $literal;
                        break;
                    case 16:
                        $length = $reader->getUIBitsLSB(2) + 3;
                        for ($j = 0 ; $j < $length; $j++) {
                            $lit_and_dist_len_list [] = $literal;
                        }
                        $i += $length - 1;
                        break;
                    case 17:
                        $length = $reader->getUIBitsLSB(3) + 3;
                        for ($j = 0 ; $j < $length; $j++) {
                            $lit_and_dist_len_list [] = 0;
                        }
                        $i += $length - 1;
                        break;
                    case 18:
                        $length = $reader->getUIBitsLSB(7) + 11;
                        for ($j = 0 ; $j < $length; $j++) {
                            $lit_and_dist_len_list [] = 0;
                        }
                        $i += $length - 1;
                        break;
                    }
                }
                
                /*
                 * ハフマン符号長からハフマン符号テーブルを生成する。(phase2)
                 * (リテラルと距離を別々に処理)
                 */
                $lit_len_list  = array_slice($lit_and_dist_len_list, 0, $hlit);
                $dist_len_list = array_slice($lit_and_dist_len_list, $hlit);

                $huffman_reader_custom_lit = new IO_Zlib_HuffmanReader_Custom($lit_len_list);
                $huffman_reader_custom_dist = new IO_Zlib_HuffmanReader_Custom($dist_len_list);
                /*
                 * ここまでで、ハフマン符号テーブルの復元終わり。
                 * ここからが、実際のデータのデコード処理
                 */
                $data = array();
                while (true) {
                    $lit_or_len = $huffman_reader_custom_lit->getValue($reader);
                    if ($lit_or_len < 256) { // literal
                        $data []= array('Value' => $lit_or_len);
                    } else if ($lit_or_len > 256) { // length
                        $length = self::$length_table[$lit_or_len - 257];                        
                        if ($lit_or_len < 265) { // 256-265 => 0
                            $length_extend_bits = 0;
                        } else if ($lit_or_len < 285) {
                            $length_extend_bits = floor(($lit_or_len - 261) / 4);
                        } else { // 285 => 0
                            $length_extend_bits = 0;
                        }
                        if ($length_extend_bits == 0) {
                            $length_extend = 0;
                        } else {
                            $length_extend = $reader->getUIBitsLSB($length_extend_bits);
                        }
                        $distance_value = $huffman_reader_custom_dist->getValue($reader);
                        $distance = self::$distance_table[$distance_value];
// echo "ZZZ: distance=$distance distance_value:$distance_value\n";
                        if ($distance_value < 4) {
                            $distance_extend_bits = 0;
                        } else {
                            $distance_extend_bits = floor(($distance_value - 3) / 2);
                        }
                        if ($distance_extend_bits == 0) {
                            $distance_extend = 0;
                        } else {
                            $distance_extend = $reader->getUIBitsLSB($distance_extend_bits);
                        }
                        $data []= array('Length' => $length,
                                        'LengthExtend' => $length_extend,
                                        'Distance' => $distance,
                                        'DistanceExtend' => $distance_extend);
                    } else { // 256:End
//                        $data []= array('Value' => $value);
                        break;
                    }
                } // while end
                $block['Data'] = $data;
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
        if ($reader->hasNextData(4)) { // XXX
            $this->adler32 = $reader->getUI32BE();
        } else {
            $this->adler32 = null;
        }
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
            case 0: // uncomress
                echo "LEN:{$block['LEN']} NLEN:{$block['NLEN']} Data:{$block['Data']}\n";
                break;
            case 1: // fixed huffman
            case 2: // dynamic huffman
                foreach ($block['Data'] as $value) {
                    if (isset($value['Value'])) {
                        printf("%02X(%c) ", $value['Value'], $value['Value']);
                    } else if (isset($value['Length'])) {
                        echo "Length:{$value['Length']}+{$value['LengthExtend']} Distance:{$value['Distance']}+{$value['DistanceExtend']} ";
                    } else { // Maybe Terminate Value
                        printf("%d(Terminate)", $value['Value']);
                    }
                }
                echo "\n";
                break;
            }
        }
        echo "ADLER32:{$this->adler32}\n";
    }

    /*
     * uncompress method
     */
    function inflate($zipdata) {
        $this->parse($zipdata);
        $data = '';
        foreach ($this->compressed_data as $block) {
            switch ($block['BTYPE']) {
            case 0: // uncompress
                $data .= $block['Data'];
                break;
            case 1: // fixed huffman
            case 2: // dynamic huffman
                foreach ($block['Data'] as $value) {
                    if (isset($value['Value'])) {
                        $data .= chr($value['Value']);
                    } else {
                        $length = $value['Length'] + $value['LengthExtend'];
                        $distance = $value['Distance'] + $value['DistanceExtend'];
                        $data_len = strlen($data);
                        if ($data_len < $distance) {
                            throw new Exception("data_len:$data_len < distance:$distance({$value['Distance']}+{$value['DistanceExtend']})");
                        }
                        $start_pos = $data_len - $distance;
                        $end_pos = $start_pos  + $length;
                        for ($i = $start_pos ; $i <= $end_pos ; $i++) { 
                            $data .= $data[$i];
                        }
                    }
                }
                break;
            }
        }
        return $data;
    }

    /*
     * compress method
     * type:0(no compression) only
     */
    function deflate() {
        ;
    }
}
