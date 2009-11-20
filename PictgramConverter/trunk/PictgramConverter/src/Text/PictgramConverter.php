<?php
/**
 * PictgramConverter
 *
 * @access  public
 * @author  takada-at<takada-at@klab.jp>
 * @create  2009/11/01
 **/
class PictgramConverter
{
    const DOCOMO = 1;
    const EZWEB = 2;
    const SOFTBANK = 3;
    const APC = false;
    private static $loaded = false;
    private static $datadir = "";
    private static $cacheFile = "";
    private static $convertFrom;
    private static $convertMap;
    private static $carrierMap;
    private static $cacheKey = "pictgram_converter";
    private static $pictRegUTF8 = "/[\xEE\x80\x80-\xEF\x83\xBC]/u";
    private static $pictRegSJIS1 = "/[\xEE\x98\xBE-\xEE\x9D\x97]/u";
    private static $pictRegSJIS2 = "/[\xEE\x88\xB4-\xEE\x97\x9F]/u";
    private static $pictRegSJIS3 = "/[\xE5\x8A\xAF-\xEF\xA8\xA8]/u";
    private static $controllSequence = '$$';



    /**
     * 文字コード変換(sjis->utf8) + 絵文字の変換(sjis->utf8)
     *   usage: PictgramConverter::convert($str, PictgramConverter::DOCOMO);
     * @access    public
     * @param     String    $str    変換する文字列
     * @param     Int       $carrier   絵文字のキャリア
     * @return    String    変換結果
     **/
    public static function convert($str, $carrierCode) {
        if(!self::$loaded){
            self::init();
        }
        //$str = self::backslash($str);
        $carrier = $carrierCode;
        //return self::_sjisConv($str, $carrier);
        if($carrier==self::DOCOMO){
            return mb_convert_encoding($str, "UTF-8", "SJIS-WIN");
        }else if($carrier==self::EZWEB){
            return strtr(mb_convert_encoding($str, "UTF-8", "SJIS-WIN"), self::$convertFrom[$carrier]);
        }else{
            return strtr(mb_convert_encoding($str, "UTF-8", "SJIS-WIN"), self::$convertFrom[$carrier]);
        }
    }

    /**
     * 絵文字の変換(utf-8->sjis) + キャリア間のマッピング
     *   usage: PictgramConverter::restore($str, PictgramConverter::DOCOMO);
     * @access    public
     * @param     String    $str    変換する文字列
     * @param     Int       $carrier   絵文字のキャリア
     * @return    String    変換結果
     **/
    public static function restore($str, $carrierCode){
        if(!self::$loaded){
            self::init();
        }
        $carrier = $carrierCode;
        return mb_convert_encoding(strtr($str, self::$convertMap[$carrier]), "SJIS-WIN", "UTF-8");
        //return self::_restoreUTF8($str, $carrier);
    }

    /**
     * utf-8絵文字をキャリア間で変換。文字コード変換はしない。
     * @access    public
     * @param     String    $str    変換する文字列
     * @param     Int       $carrier   絵文字のキャリア
     * @return    String    変換結果
     **/
    public static function convertCarrier($str, $carrierCode) {
        if(!self::$loaded){
            self::init();
        }
        $carrier = $carrierCode;
        return strtr($str, self::$carrierMap[$carrier]);
    }

    /**
     * 絵文字の表示: unicode文字列で指定。
     * @public    String    $unicode    表示する絵文字を示すunicode16進文字列
     * @return    String    絵文字バイナリ(utf-8)
     */
    public static function display($unicode){
        if(!self::$loaded){
            self::init();
        }
        return mb_convert_encoding(self::hex2bin($unicode), "UTF-8", "Unicode");
    }

    /**
     * 絵文字の判別
     */
    public static function isPict($binary, $carrier, $encoding="SJIS-WIN"){
        if(!self::$loaded)self::init();

        if($binary=="")return false;
        switch($encoding){
        case "SJIS-WIN":
            return array_key_exists($binary, self::$convertFrom[$carrier]);
        case "UTF-8":
            return array_key_exists($binary, self::$convertMap[$carrier]);
        }
    }

    /**
     * 絵文字の判別
     */
    public static function hasPict($binary, $carrier, $encoding="UTF-8"){
        if($encoding=="UTF-8"){
            return self::_hasPictUTF8($binary, $carrier);
        }
    }

    public static function getData(){
        if(!self::$loaded)self::init();
        $c = array(
                   "convertFrom" => self::$convertFrom,
                   "convertMap" => self::$convertMap,
                   "carrierMap" => self::$carrierMap
        );
        return $c;
    }


    /**
     * データファイルのロード
     */
    public static function init(){
        if(!self::$loaded){
            $root = realpath(dirname(__FILE__));
            $path = $root . DIRECTORY_SEPARATOR."PictgramConverter/data";
            self::$datadir = $path;
            self::$cacheFile = self::$datadir .DIRECTORY_SEPARATOR . self::$cacheKey . ".cache";
            self::loadData();
        }
    }


    /* private methods */
    private static function backslash($str){
        return stripslashes($str);
    }

    private static function createMapping($path){
        mb_internal_encoding("UTF-8");
        $emojiList = array();
        $emojiMap = array();
        $convertFrom = array();
        $convertMap = array(self::DOCOMO =>array(),
                                  self::EZWEB =>array(),
                                  self::SOFTBANK=>array());
        $carrierMap = array(self::DOCOMO =>array(),
                                  self::EZWEB =>array(),
                                  self::SOFTBANK=>array());
        $defaultutf = array();

        foreach(array(self::DOCOMO, self::EZWEB, self::SOFTBANK) as $ca){
            $defaultutf[$ca] = array();
            $cname = self::cname($ca);
            $emojiList = array_merge($emojiList,
                                           json_decode(file_get_contents($path .DIRECTORY_SEPARATOR."{$cname}_emoji.json"), true));
            $emojiMap = array_merge($emojiMap,
                                          json_decode(file_get_contents($path .DIRECTORY_SEPARATOR."{$cname}_convert.json"), true));
            $convertFrom[$ca] = array();

            if($ca == self::DOCOMO){
                continue;
            }
            foreach($emojiList[$cname] as $list){
                if($list["sjis"]==null)continue;
                $input = mb_convert_encoding(pack("H*", $list["sjis"]), "UTF-8", "SJIS-WIN");
                if($ca==self::EZWEB){
                    $convertFrom[$ca][ $input ] = self::hex2bin($list["utf-8-form"]);
                }
                else{
                    $convertFrom[$ca][ $input ] = self::hex2bin($list["utf-8"]);
                }
            }
        }
        foreach(array(self::DOCOMO, self::EZWEB, self::SOFTBANK) as $ca){
            $cname = self::cname($ca);
            $is_kddi = $ca == self::EZWEB;
            foreach($emojiMap[$cname] as $map){
                $data = $emojiList[$cname][$map[$cname]];
                $input = ($is_kddi ? pack("H*", $data["utf-8-form"]) : pack("H*", $data["utf-8"]));

                //同一キャリアでの変換
                $convertMap[$ca][ $input ] = mb_convert_encoding(self::hex2bin($data["sjis"]), "UTF-8", "SJIS-WIN");
                $carrierMap[$ca][ $input ] = $input;

                //他キャリアへの変換
                foreach($map as $c => $code){
                    if($c==$ca)continue;
                    $carrierMap[self::ccode($c)][$input] = self::convCode($code, $emojiList[$c], false, (self::ccode($c) == self::EZWEB));
                    if($is_kddi){
                        $convertMap[self::ccode($c)][ $input ] = self::convCode($code, $emojiList[$c]);
                    }else{
                        $convertMap[self::ccode($c)][ $input ] = self::convCode($code, $emojiList[$c]);
                    }
                }
            }
        }
        $cacheObj = array(
                          "convertFrom" => $convertFrom,
                          "convertMap" => $convertMap,
                          "carrierMap" => $carrierMap
        );
        self::$convertMap = $convertMap;
        self::$carrierMap = $carrierMap;
        self::$convertFrom = $convertFrom;
        return $cacheObj;
    }

    private static function loadData(){

        $cache = self::loadCache(self::$cacheFile);
        if($cache!=null
           && array_key_exists("convertFrom", $cache)
           && array_key_exists("convertMap", $cache)
           && array_key_exists("carrierMap", $cache)
           ){
            self::$convertFrom = $cache["convertFrom"];
            self::$convertMap = $cache["convertMap"];
            self::$carrierMap = $cache["carrierMap"];
            self::$loaded = true;
            return;
        }
        // マッピングを作成してキャッシュ
        $cache = self::createMapping(self::$datadir);
        self::$convertFrom = $cache["convertFrom"];
        self::$convertMap = $cache["convertMap"];

        self::doCache($cache);
        self::$loaded = true;
    }

    private static function loadCache($file){
        if (self::APC && function_exists("apc_fetch")){
            return apc_fetch(self::$cacheKey);
        }
        try{
            if(file_exists(self::$cacheFile)){
                return unserialize(file_get_contents($file));
                //require_once(self::$cacheFile);
            }else{
                return null;
            }
            return $data;
        }catch(Exception $e){
            return null;
        }
    }

    private static function doCache($data){
        if (self::APC && function_exists("apc_store"))
        {
            return apc_store(self::$cacheKey, $data, 0);
        }else{
            $cdata = serialize($data);
            $hd = fopen(self::$cacheFile, "w");
            fwrite($hd, $cdata);
            fclose($hd);
            return;
        }
    }
    private static function hex2bin($s){
        //echo "hex2bin$s\n";
        return pack("H*", $s);
    }
    private static function code2bin($code, $carrier, &$map, $encoding="UTF-8"){
        switch($encoding){
        case "UTF-8":
            if($carrier==self::EZWEB){
                 return self::hex2bin($map[$code]["utf-8-form"]);
            }else{
                return self::hex2bin($map[$code]["utf-8"]);
            }
        case "SJIS-WIN":
            return self::hex2bin($map[$code]["sjis"]);
        }
    }
    private static function ccode($carrier){
        if($carrier == "docomo"){
            return self::DOCOMO;
        }else if($carrier=="ezweb"){
            return self::EZWEB;
        }else{
            return self::SOFTBANK;
        }
    }
    private static function cname($carrier){
        if($carrier==self::DOCOMO){
            return "docomo";
        }else if($carrier==self::EZWEB){
            return "ezweb";
        }else{
            return "softbank";
        }
    }
    private static function convCode($code, &$list, $sjis=true, $kddi=false){
        //echo "c: $code\n";
        $ret = "";
        if(strpos($code, ";")>0){
            $l = explode(";", $code);
            foreach($l as $n){
                $ret .= self::convCode($n, $list, $sjis, $kddi);
            }
        }else if(array_key_exists($code, $list)){
            if($sjis){
                $ret = mb_convert_encoding(self::hex2bin($list[$code]["sjis"]), "UTF-8", "SJIS-WIN");
            }else{
                $kddi ? $ret = self::hex2bin($list[$code]["utf-8-form"])
                    : $ret = self::hex2bin($list[$code]["utf-8"]);
            }
        }else{
            $ret = $code;
        }
        //echo "ret: $ret\n";
        return $ret;
    }

    private static function dump_hash($h){
        if(is_array($h)){
            $s = "";
            $s .= "array(\n";
            $i = true;
            foreach($h as $k => $v){
                if($i){ $i = false; }
                else{ $s .= ",\n"; }
                $s .= "    " . self::dump_hash($k) . " => ";
                if(is_array($v)){
                    $s .= "\n";
                    $s .= preg_replace("/^/", "    ", self::dump_hash($v));
                }else{
                    $s .= self::dump_hash($v);
                }
            }
            $s .= "\n)";
            return $s;
        }else if(is_string($h)){
            return '\'' . $h . '\'';
        }else{
            return $h;
        }
    }
}