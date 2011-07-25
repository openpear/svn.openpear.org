<?php

require_once('IO/Bit.php');


class IO_Zlib_HuffmanReader {
    function getNextValue(&$reader) {
        ;
    }
    function matchValue($value, $bit_width) { ; } // must be overrided.
}

/*
 * BTYPE:1 Fixed Huffman 用 (決め打ち)
 */
class IO_Zlib_HuffmanReader_Fixed288 extends IO_Zlib_HuffmanReader {
    function matchValue($value, $bit_width) {
        ; // if でベタに作る
    }
}

/*
 * BTYLE:2 Custom Huffma 用
 */
class IO_Zlib_HuffmanReader_Custom extends IO_Zlib_HuffmanReader {
    function __construct($huffman_code_length_table) {
        ; // ハフマン符号長からテーブルを復元する処理
    }
    function matchValue($value, $bit_width) {
        ; // 逆引きテーブルを参照する
    }
}

class IO_Zlib {
    var $cmf;
    var $flg;
    var $dictid = null;
    var $compressed_data = null;
    // fixed huffman table (length, distance)
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
//            echo "dictid:{$this->dictid}\n";
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

                /*
                 * まず、ハフマン符号化されたハフマンテーブルを復元する
                 */

                $hlit  = $reader->getUIBitsLSB(5) + 257;
                $hdist = $reader->getUIBitsLSB(5) + 1;
                $hclen = $reader->getUIBitsLSB(4) + 4;
                echo "hlit:$hlit hdist:$hdist hclen:$hclen\n";
                $block['HLIT'] = $hlit;
                $block['HDIST'] = $hdist;
                $block['HCLEN'] = $hclen;
                /*
                 * ハフマンテーブルのハフマン符号長を読み取る (phase 1)
                 */
                $hclen_order = array(16, 17, 18,
                                     0,  8, 7,  9, 6, 10, 5, 11,
                                     4, 12, 3, 13, 2, 14, 1, 15);
                $hclen_table = array_fill(0, 19, 0);
                for ($i = 0 ; $i < $hclen ; $i++) {
                    $value = $reader->getUIBitsLSB(3);
                    $hclen_table[$hclen_order[$i]] = $value;
                }
                $block['HCLEN_TABLE'] = $hclen_table;
                /*
                 * ハフマン符号長からハフマン符号テーブルを生成して、
                 * 逆引きリストを得る。(phase 1)
                 */
                $huffman_table_rev = self::huffman_table_from_hclen($hclen_table);
//      var_dump($huffman_table_rev); 
                $block['HCCODE_TABLE_REV'] = $huffman_table_rev; // 逆引きリスト
                /*
                 * ハフマンテーブルのハフマン符号長を読み取る (phase 2)
                 */
                $lit_and_dist_len_list = array();
                $lit_and_dist_count = $hlit + $hdist;
                $huffman_table_rev_keys = array_keys($huffman_table_rev);
                $hlen_min = min($huffman_table_rev_keys);
                $hlen_max = max($huffman_table_rev_keys);
//  echo "hlen_min:$hlen_min hlen_max:$hlen_max\n";
//  echo "getOffset"; var_dump($reader->getOffset());  echo "\n";
                for ($i = 0 ; $i < $lit_and_dist_count ; $i++) {
                    $value = 0;
                    for ($j = 0 ; $j < $hlen_min ; $j++) {
                        $value = ($value << 1) | $reader->getUIBitLSB();
                    }
                    foreach (range($hlen_min, $hlen_max) as $key) {
                        $hccode_list = $huffman_table_rev[$key];
                        if (isset($hccode_list[$value])) {
//                            echo "XXX: hccode_list value:".$hccode_list[$value]."\n";
                            $lit_or_dist = $hccode_list[$value];
                            switch ($lit_or_dist) {
                            default: // 1-15
//                                echo "XXX($i): $lit_or_dist:\n";
                                $literal = $lit_or_dist;
                                $lit_and_dist_len_list [] = $literal;
                                break 2;
                            case 16:
                                $length = $reader->getUIBitsLSB(2) + 3;
//                                echo "XXX($i): 16:length=$length\n";
                                for ($j = 0 ; $j < $length; $j++) {
                                    $lit_and_dist_len_list [] = $literal;
                                }
                                $i += $length - 1;
                                break 2;
                            case 17:
                                $length = $reader->getUIBitsLSB(3) + 3;
//                                echo "XXX($i): 17:length=$length\n";
                                for ($j = 0 ; $j < $length; $j++) {
                                    $lit_and_dist_len_list [] = 0;
                                }
                                $i += $length - 1;
                                break 2;
                            case 18:
                                $length = $reader->getUIBitsLSB(7) + 11;
//                                echo "XXX($i): 18:length=$length\n";
                                for ($j = 0 ; $j < $length; $j++) {
                                    $lit_and_dist_len_list [] = 0;
                                }
                                $i += $length - 1;
                                break 2;

                            }
                        }
                        $value = ($value << 1) | $reader->getUIBitLSB();
                    }
                }
//                print_r($lit_and_dist_len_list);
//                $block['LIT&DIST_LEN_LIST'] = $lit_and_dist_len_list;
                /*
                 * ハフマン符号長からハフマン符号テーブルを生成して、
                 * 逆引きリストを得る。(phase 2)
                 * (リテラルと距離を別々に処理)
                 */
                $lit_len_list =  array_slice($lit_and_dist_len_list, 0, $hlit);
                $dist_len_list =  array_slice($lit_and_dist_len_list, $hlit);
                $lit_huffman_table_rev = self::huffman_table_from_hclen($lit_len_list);
                $dist_huffman_table_rev = self::huffman_table_from_hclen($dist_len_list);
                var_dump($lit_huffman_table_rev);
                exit(0);
                $block['LIT&DIST_HUFFMAN_TABLE_REV'] = $lit_and_dist_huffman_table_rev;
                /*
                 * ここまでで、ハフマン符号テーブルの復元終わり。
                 * ここからが、実際のデータのデコード処理
                 */
                $data = array();
                $lit_and_dist_huffman_table_rev_keys = array_keys($lit_and_dist_huffman_table_rev);- ハフマン符号長からテーブルを復元する処理を function にまとめた
                                                                                                       - 符号の割り当て処理を修正 (余計な事してたので)
                                                                                                       - 16～18 の処理を追加 (ハフマンテーブル復元用ハフマンテーブルの repeat 処理)
                                                                                                       - リテラルと距離のハフマンテーブルを別なので、処理を分ける

                                                                                                       - 後でクラスにまとめる予定 >IO_Zlib_Huffman
                                                                                                       - 最後に実装する > inflate, deflate
                                                                                                       TODO: Custom Huffman の長さ/距離の処理
                                                                                                       
                $hlen_min = min($lit_and_dist_huffman_table_rev_keys);
                $hlen_max = max($lit_and_dist_huffman_table_rev_keys);
                while (true) {
                    $value = 0;
                    for ($i = 0 ; $i < $hlen_min ; $i++) {
                        $value = ($value << 1) | $reader->getUIBitLSB();
                    }
                    foreach (range($hlen_min, $hlen_max) as $key) {
                        $hccode_list = $lit_and_dist_huffman_table_rev[$key];
                        if (isset($hccode_list[$value])) {
                            $real_value = $hccode_list[$value];
                            break ;
                        }
                        $value = ($value << 1) | $reader->getUIBitLSB();
                    }
                    if ($real_value < 256) {
                        $data []= array('Value' => $value,
                                        'RealValue' => $real_value);
                    } if ($real_value > 256) {
                        $data []= array('Value' => $value, "NO" => "????????");
                        break;
                    } else { // 256:End
                        
                        $data []= array('Value' => $value);
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

    /*
     * uncompress method
     */
    function inflate($zipdata) {
        $this->parse($zipdata);
    }

    /*
     * compress method
     * type:0(no compression) only
     */
    function deflate() {
        ;
    }
    /*
     * 符号長テーブルを元にハフマンテーブルを復元
     */
    function huffman_table_from_hclen($hclen_table) {
        if (! is_array($hclen_table)) {
            throw new Exception("huffman_table_from_hclen(hclen_table=$hclen_table)");
        }
        $hclen_table_len = count($hclen_table);
        $hclen_min = 128; // XXX
        $hclen_max = 0;
        for ($i = 0 ; $i < $hclen_table_len; $i++) {
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
        //         echo "hclen_min:$hclen_min hclen_max:$hclen_max\n";
        if ($hclen_min > $hclen_max) {
            throw new Exception("huffman_table_from_hclen: hclen_min($hclen_min) > hclen_max($hclen_max)");
        }
        $hclen_lists = array_fill($hclen_min, $hclen_max - $hclen_min, array());
        $hccode_table_rev = array();
        $value = 0;
        for ($i = $hclen_min ; $i <= $hclen_max ; $i++) {
            $hccode_table_rev[$i] = array();
            for ($j = 0 ; $j < $hclen_table_len; $j++) {
                if ($hclen_table[$j] == $i) {
                    $hccode_table_rev[$i][$value] = $j;
                    $value ++;
                }
            }
/* 没 (この処理は入れちゃダメ)
            // 念の為 小さい方のlength に死に値があった場合用に
            // 使用した bit の右側を全て埋める処理
            $value --;
            for ($value_bit = $value >> 1; $value_bit > 0 ; $value_bit >>= 1) {
                $value |= $value_bit;
            }
            $value ++;
*/
            $value *= 2;
        }
        return $hccode_table_rev;
    }
}
