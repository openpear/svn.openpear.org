<?php
class Net_MobileCIDR_SoftBank extends Net_MobileCIDR_Carrier{
  protected $target_url = "http://creation.mb.softbank.jp/web/web_ip.html";
  protected $regexp = '!<td bgcolor="#eeeeee">&nbsp;&nbsp;([\d\./]+)</td>!';
}
