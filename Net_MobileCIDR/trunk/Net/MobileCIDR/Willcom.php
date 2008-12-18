<?php
class Net_MobileCIDR_Willcom extends Net_MobileCIDR_Carrier{
  protected $target_url = "http://www.willcom-inc.com/ja/service/contents_service/club_air_edge/for_phone/ip/";
  protected $regexp = '!<font size="2">([\d\./]+)</font>!';
}
