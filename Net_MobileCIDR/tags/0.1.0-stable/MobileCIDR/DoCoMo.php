<?php
class Net_MobileCIDR_DoCoMo extends Net_MobileCIDR_Carrier{
  protected $target_url = "http://www.nttdocomo.co.jp/service/imode/make/content/ip/";
  protected $regexp = '!<li>([\d\./]+)</li>!';

  public function getContents(){
    $regexp = '!<ul class="normal txt">.+?</ul>!s';
    $data = parent::getContents();
    preg_match($regexp,$data,$matches);
    return $matches[0];
  }

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
