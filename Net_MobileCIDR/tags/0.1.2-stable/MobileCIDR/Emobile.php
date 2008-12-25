<?php
class Net_MobileCIDR_EMobile extends Net_MobileCIDR_Carrier{
  protected $target_url = "http://developer.emnet.ne.jp/ipaddress.html";
  protected $regexp = '!<div align="center">([\d\./]+)</div>!';
}
