<?php
/**
* Net_MobileCIDR
* Scraping Gateway
*
* @version: 0.1.1 <2008/12/19 7:29>
* @package: Net_MobileCIDR
* @author: Shuhei Tanuma <shuhei.tanuma at gmail.com>
**/

require dirname(__FILE__) . "/MobileCIDR/Interface.php";
require dirname(__FILE__) . "/MobileCIDR/Carrier.php";
require dirname(__FILE__) . "/MobileCIDR/DoCoMo.php";
require dirname(__FILE__) . "/MobileCIDR/SoftBank.php";
require dirname(__FILE__) . "/MobileCIDR/EZweb.php";
require dirname(__FILE__) . "/MobileCIDR/Willcom.php";
require dirname(__FILE__) . "/MobileCIDR/Emobile.php";

class Net_MobileCIDR{
  const Version = "0.1.1";
  const DOCOMO = 1;
  const SOFTBANK = 2;
  const EZWEB = 3;
  const WILLCOM = 4;
  const EMOBILE = 5;

  public static function Factory($carrier){
    switch($carrier){
      case Net_MobileCIDR::DOCOMO:
        return new Net_MobileCIDR_DoCoMo();
        break;
      case Net_MobileCIDR::SOFTBANK:
        return new Net_MobileCIDR_SoftBank();
        break;
      case Net_MobileCIDR::EZWEB:
        return new Net_MobileCIDR_EZweb();
        break;
      case Net_MobileCIDR::WILLCOM:
        return new Net_MobileCIDR_Willcom();
        break;
      case Net_MobileCIDR::EMOBILE:
        return new Net_MobileCIDR_Emobile();
        break;
    }
  }

  public static function checkCIDR($ip){
    list($ip,$mask) = explode("/",$ip);

    if(isset($ip) && ($mask > 0 && $mask <= 32)){
      $iplist = explode(".",$ip);
      if(count($iplist) == 4){
        for($i=0; $i<=4;$i++)
          if(!($iplist[$i] >= 0x0 && $iplist[$i] <=0xff)) return false;
      }else{
        return false;
      }
      return true;
    }else{
      return false;
    }
  }
}
