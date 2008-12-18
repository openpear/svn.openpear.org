<?php
require dirname(__FILE__) . "/MobileCIDR/Interface.php";
require dirname(__FILE__) . "/MobileCIDR/Carrier.php";
require dirname(__FILE__) . "/MobileCIDR/DoCoMo.php";
require dirname(__FILE__) . "/MobileCIDR/SoftBank.php";
require dirname(__FILE__) . "/MobileCIDR/EZweb.php";
require dirname(__FILE__) . "/MobileCIDR/Willcom.php";
require dirname(__FILE__) . "/MobileCIDR/Emobile.php";

class Net_MobileCIDR{
  const DoCoMo = 1;
  const SoftBank = 2;
  const EZweb = 3;
  const Willcom = 4;
  const EMobile = 5;

  public static function Factory($carrier){
    switch($carrier){
      case Net_MobileCIDR::DoCoMo:
        return new Net_MobileCIDR_DoCoMo();
        break;
      case Net_MobileCIDR::SoftBank:
        return new Net_MobileCIDR_SoftBank();
        break;
      case Net_MobileCIDR::EZweb:
        return new Net_MobileCIDR_EZweb();
        break;
      case Net_MobileCIDR::Willcom:
        return new Net_MobileCIDR_Willcom();
        break;
      case Net_MobileCIDR::EMobile:
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
