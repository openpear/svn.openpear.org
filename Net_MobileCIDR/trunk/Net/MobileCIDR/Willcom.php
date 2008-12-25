<?php
class Net_MobileCIDR_Willcom extends Net_MobileCIDR_Carrier{
  protected $target_url = "http://www.willcom-inc.com/ja/service/contents_service/create/center_info/index.html";
  protected $regexp = '!<font size="2">([\d\./]+)</font>!';
}
