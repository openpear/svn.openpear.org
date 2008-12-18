<?php
class Net_MobileCIDR_EZweb extends Net_MobileCIDR_Carrier{
  protected $target_url = "http://www.au.kddi.com/ezfactory/tec/spec/ezsava_ip.html";
  protected $regexp = '!<td>\s*<div class="TableText">(.*?)</div>\s*</td>\s*<td>\s*<div class="TableText">(.*?)</div>\s*</td>!';

  protected function makeCIDR($array){
    return $array[1] . $array[2];
  }
}
