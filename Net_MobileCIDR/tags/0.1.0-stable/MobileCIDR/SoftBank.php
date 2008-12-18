<?php
class Net_MobileCIDR_SoftBank extends Net_MobileCIDR_Carrier{
  protected $target_url = "http://creation.mb.softbank.jp/web/web_ip.html";
  protected $regexp = '!<td bgcolor="#eeeeee">&nbsp;&nbsp;([\d\./]+)</td>!';

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
