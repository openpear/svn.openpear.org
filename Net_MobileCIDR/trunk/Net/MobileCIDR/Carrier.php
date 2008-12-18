<?php
abstract class Net_MobileCIDR_Carrier implements Net_MobileCIDR_Interface{
  protected $target_url;
  protected $regexp;
  protected $useragent = "PHP/Net_MobileCIDR/1.0";
  
  public function getContents(){
    $url_params = parse_url($this->target_url);

    $sock = fsockopen($url_params['host'],80,$errno,$errstr,3);
    if($sock){
      $request  = sprintf("GET /%s%s HTTP/1.0\r\n",$url_params['path'],
        (isset($url_params['query'])) ? "?" . $url_params['query'] : null);
      $request .= sprintf("Host: %s\r\n",$url_params['host']);
      $request .= sprintf("UserAgent: %s\r\n",$this->useragent);
      $request .= sprintf("\r\n");
      
      fwrite($sock,$request);
      $flag = false;
      $data = "";
      while(!feof($sock)){
        $line = fgets($sock);
        if(!flag){
          if($line == "\r\n"){
            $flag = true;
          }
        }else{
          $data .= $line;
        }
      }
    }
    
    return $data;
  }

  public function getIPAddresses(){
    return $this->Scrape();
  }
  
  abstract function Scrape();
}