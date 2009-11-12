<?php

require_once(dirname(__FILE__).'/lime.php');
require_once("Text/PictgramConverter.php");
//require_once(dirname(__FILE__).'/../HTML_PictgramConverter/src/HTML/PictgramConverter.php');


$t = new lime_test(null, new lime_output_color());

define("DOCOMO", 1);
define("EZWEB", 2);
define("SOFTBANK", 3);

function hex2bin($s){
    $r = array("C*");
    for($i=0,$l=strlen($s);$i<$l;$i+=2){
        //echo $s[$i].$s[$i+1]."\n";
        $r[] = intval($s[$i].$s[$i+1], 16);
    }
    return call_user_func_array("pack", $r);
}
function s($s){
    return mb_convert_encoding($s, "SJIS", "UTF-8");
}

foreach(array(array("F89F", "EE98BE", DOCOMO),
              array("F3CB", "EEB38B", EZWEB),
              array("F659", "EEBD99", EZWEB),
              array("F65A", "EEBD9A", EZWEB),
              array("F6DA", "EEBF9A", EZWEB),
              array("F68D", "EEBE8D", EZWEB),
              array("F95F", "EE809F", SOFTBANK)) as $case){
    //docomo
    $in = hex2bin($case[0]);
    $out = hex2bin($case[1]);
    $s1 = "あいうえお";
    $s2 = "aaa";
    $r = PictgramConverter::convert(s($s1) . $in . s($s2) , $case[2], "SJIS-WIN");
    $t->ok($s1 .$out .$s2==$r, "convert: ".$r . ": ".strtoupper(bin2hex($r)));
}
foreach(array(array("EE98BE", hex2bin("F89F"), DOCOMO), //docomo[1]
              array("EE98BE", hex2bin("F660"), EZWEB), //docomo[1]
              array("EE98BE", hex2bin("F98B"), SOFTBANK), //docomo[1]
              array("EEBD9B", s("？"), DOCOMO), //ez[3]
              array("EEBD9B", hex2bin("F65B"), EZWEB),
              array("EEBD9B", hex2bin("F960"), SOFTBANK),
              array("EE8081", hex2bin("F941"), SOFTBANK), //sb[1]
              array("EE8081", hex2bin("F995"), DOCOMO), //sb[1]
              array("EE8081", hex2bin("F6D5"), EZWEB)
              ) as $case){
    //docomo
    $in = hex2bin($case[0]);
    $out = $case[1];
    $s1 = "かきくけこ124";
    $s2 = "aaa表示";
    $r = PictgramConverter::restore($s1 .$in .$s2, $case[2]);
    $t->ok(s($s1) .$out .s($s2)==$r, "recover: ".$r . ":" . strtoupper(bin2hex($r)));
    $str = $s1 .$in .$s2;
}

$nonp = pack("H*", "EE8B90");
$str = "aaaa" . $nonp . "bbb";
$str2 = PictgramConverter::restore($str, DOCOMO);
$t->cmp_ok(strlen($str2), ">", 0, "non pict");

/*
foreach(array(array("F89F", hex2bin("EE98BE"), DOCOMO), //docomo[1]
              array("F65B", hex2bin("EEBD9B"), EZWEB)
              ) as $case){
    //docomo
    $in = hex2bin($case[0]);
    $out = $case[1];
    $s1 = "かきくけこ124";
    $s2 = "aaa表示";
    $r = PictgramConverter::convert($s1 .$in .$s2, $case[2], "UTF-8");
    $t->ok($s1 .$out .$s2==$r, "convert: ".$r . ":" . strtoupper(bin2hex($r)));
}
*/


foreach(array(array("E63E", "EE98BE"),
              array("E68F", "EE9A8F"),
              array("E756", "EE9D96"))
        as $case){
    $r = PictgramConverter::display($case[0]);
    $t->ok($r==hex2bin($case[1]), "convert: " . strtoupper(bin2hex($r)));
}


function checkAllPict(){
    global $t;
    $dir = dirname(__FILE__) .'/../src/Text/PictgramConverter/data/';
    $list = array();
    $map = array();
    foreach (array("docomo", "ezweb", "softbank") as $cname) {
        $list = array_merge($list,
                            json_decode(file_get_contents($dir ."{$cname}_emoji.json"), true));
        $map = array_merge($map,
                           json_decode(file_get_contents($dir ."{$cname}_convert.json.bak"), true));
    }
    foreach(array("docomo", "ezweb", "softbank") as $cname){
        switch($cname){
        case "docomo": $c = 1;break;
        case "ezweb" : $c = 2;break;
        case "softbank" : $c = 3;break;
        }
        foreach ($list[$cname] as $code) {
            if($code["sjis"] == ""){
                continue;
            }
            if($c!=2){
                $t->cmp_ok(PictgramConverter::convert((pack("H*", $code["sjis"])), $c),
                           "==", pack("H*", $code["utf-8"]), "conv " . $cname .$code["number"]);
            }else{
                $t->cmp_ok(PictgramConverter::convert((pack("H*", $code["sjis"])), $c),
                           "==", pack("H*", $code["utf-8-form"]), "conv " . $cname .$code["number"]);
            }
        }
    }
}
function checkAllPictU(){
    global $t;
    $dir = dirname(__FILE__) .'/../src/Text/PictgramConverter/data/';
    $list = array();
    $map = array();
    foreach (array("docomo", "ezweb", "softbank") as $cname) {
        $list = array_merge($list,
                            json_decode(file_get_contents($dir ."{$cname}_emoji.json"), true));
        $map = array_merge($map,
                           json_decode(file_get_contents($dir ."{$cname}_convert.json"), true));
    }
    $ulist = array();
    $count = array();
    $defaultutf = array();
    foreach(array("docomo", "ezweb", "softbank") as $cname){
        switch($cname){
        case "docomo": $c = 1;break;
        case "ezweb" : $c = 2;break;
        case "softbank" : $c = 3;break;
        }
        $count[$cname] = 0;
        $ulist[$c] = array();
        $slist = array();
        $defaultutf[$cname] = array();
        foreach ($list[$cname] as $code) {
            if($code["sjis"] == ""){
                continue;
            }
            $scode = $code["sjis"];
            $sbin = pack("H*", $code["sjis"]);
            $defaultutf[$cname][] = intval(bin2hex(mb_convert_encoding($sbin, "UTF-8", "SJIS-WIN")), 16);

            if($c==1){
                $ucode = $code["utf-8"];
                $ubin1 = pack("H*", $code["utf-8"]);
                $ubin2 = mb_convert_encoding($sbin, "UTF-8", "SJIS-WIN");
            }else if($c==3){
                $ucode = $code["utf-8"];
                $ubin1 = pack("H*", $code["utf-8"]);
                $ubin2 = mb_convert_encoding($sbin, "UTF-8", "SJIS-WIN");
            }else{
                $ucode = $code["utf-8-form"];
                $ubin1 = pack("H*", $code["utf-8-form"]);
                $i = intval($code["sjis"], 16);
                $i -= 1792;
                $ubin2 = mb_convert_encoding(pack("H*", strtoupper(base_convert($i, 10, 16))), "UTF-8", "Unicode");
            }

            if($ubin1!=$ubin2){
                $i1 = intval(bin2hex($ubin1), 16);
                $i2 = intval(bin2hex($ubin2), 16);
                if($c==2){
                    echo strtoupper(bin2hex($ubin1));
                    echo " : ";
                    echo strtoupper(bin2hex($ubin2));
                    echo " : ";
                    echo $scode;
                    echo "\n";
                }

                //echo $cname . ": " . ($i1-$i2) . "\n";
                //echo $ucode . " != " . strtoupper(bin2hex($ubin2)) . "  ::$cname\n";
                $ulist[$c][$ubin2] = $ubin1;
                ++$count[$cname];
            }

        }
    }
    foreach (array("docomo", "ezweb", "softbank") as $c) {
        sort($defaultutf[$c]);
        $min = base_convert($defaultutf[$c][0], 10, 16);
        $max = base_convert($defaultutf[$c][count($defaultutf[$c])-1], 10, 16);
        echo "$c : $min - $max\n";
    }
    foreach ($count as $c=>$v) {
        echo $c . ": " . $v . "/ " . count($list[$c])  ."\n";
    }
}
//checkAllPictU();
checkAllPict();


foreach(array(array("EE9992", hex2bin("F6E6"), EZWEB),
              array("EE9992", hex2bin("F9B9"), SOFTBANK),
              array("EE9A9A", s("［ﾒｶﾞﾈ］"), SOFTBANK),
              array("EEBFAA", hex2bin("F8D7"), DOCOMO),
             array("EEBFAA", hex2bin("F6EA"), EZWEB),
              array("EF818F", s("［i］"), DOCOMO),
              array("EF818F", s("［i］"), SOFTBANK),
              array("EE808F", hex2bin("F94F"), SOFTBANK),
              array("EE808F", hex2bin("F9CC"), DOCOMO),
              array("EE808F", hex2bin("F6CF"), EZWEB),
              array("EE80A0", hex2bin("F960"), SOFTBANK),
              array("EE80A0", s("?"), DOCOMO),
              array("EE80A0", hex2bin("F65B"), EZWEB)
              ) as $case){
    $in = hex2bin($case[0]);
    $out = $case[1];
    $s1 = "かきくけこ124";
    $s2 = "aaa表示";
    $r = PictgramConverter::restore($s1 .$in .$s2, $case[2]);
    $t->ok(s($s1) .$out .s($s2)==$r, "recover: ".$r . ":" . strtoupper(bin2hex($r)));
}
$data = pack("H*", "e38195e381a8eeb1b8");
$r = PictgramConverter::restore($data, DOCOMO);
$sjisd = mb_convert_encoding("さと", "SJIS-WIN", "UTF-8") . pack("H*", "F8EE");
$t->cmp_ok($r, "==", $sjisd, "restore");


$str = pack("H*","efbe8aefbdb9efbe9de381aee59381e6a0bce3818ce381a0e38184e38199e3818de381a0e381a3e3819fe3818be38289e881b4e3818fe381a8efbe83efbe9defbdbcefbdaeefbe9deeb3aeeeb3aeeeb1bee69bb2e887aae4bd93e38282e3818be381aae3828ae38199e3818d282d3aeeb1b9");
$r = PictgramConverter::restore($str, DOCOMO);
$last = mb_substr($r, mb_strlen($r)-1);
$t->cmp_ok($last, "==", pack("H*", "F991"), "restore");
