<?php
class Net_MobileCIDR_EZweb extends Net_MobileCIDR_Carrier{
  protected $target_url = "http://www.au.kddi.com/ezfactory/tec/spec/ezsava_ip.html";
  protected $regexp = '!<td>\s*<div class="TableText">(.*?)</div>\s*</td>\s*<td>\s*<div class="TableText">(.*?)</div>\s*</td>!';

  public function Scrape(){
    $result = array();
    preg_match_all($this->regexp,$this->getContents(),$matches,PREG_SET_ORDER);
    
    foreach($matches as $item){
      if(Net_MobileCIDR::checkCIDR($item[1] . $item[2])){
        $result[] = $item[1] . $item[2];
      }
    }
    
    return $result;
  }
}
