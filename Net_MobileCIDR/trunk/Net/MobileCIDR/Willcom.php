<?php
class Net_MobileCIDR_Willcom extends Net_MobileCIDR_Carrier{
  protected $target_url = "http://www.willcom-inc.com/ja/service/contents_service/club_air_edge/for_phone/ip/";
  protected $regexp = '!<font size="2">([\d\./]+)</font>!';
  
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
