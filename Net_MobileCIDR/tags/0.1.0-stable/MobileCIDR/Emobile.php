<?php
class Net_MobileCIDR_EMobile extends Net_MobileCIDR_Carrier{
  protected $target_url = "http://developer.emnet.ne.jp/ipaddress.html";
  protected $regexp = '!<div align="center">([\d\./]+)</div>!';

  public function Scrape(){
    $result = array();
    preg_match_all($this->regexp,$this->getContents(),$matches,PREG_SET_ORDER);
    foreach($matches as $item){
      if(Net_MobileCIDR::checkCIDR($item[1])){
        $result[] = $item[1];
      }
    }
    
    return $result;
  }
}
