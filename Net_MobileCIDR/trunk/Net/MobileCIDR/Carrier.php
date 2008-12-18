<?php
abstract class Net_MobileCIDR_Carrier implements Net_MobileCIDR_Interface{
  protected $target_url;
  protected $regexp;
  protected $useragent = "PHP %s/Net_MobileCIDR/%s";
  
  public function __construct(){
    $this->useragent = sprintf($this->useragent,phpversion(),Net_MobileCIDR::Version);
  }
  
  public function getContents(){
    $url_params = parse_url($this->target_url);

    $sock = @fsockopen($url_params['host'],80,$errno,$errstr,3);
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

  protected function makeCIDR($array){
    return $array[1];
  }

  public function Scrape(){
    $result = array();
    preg_match_all($this->regexp,$this->getContents(),$matches,PREG_SET_ORDER);
    foreach($matches as $item){
      if(Net_MobileCIDR::checkCIDR($this->makeCIDR($item))){
        $result[] = $this->makeCIDR($item);
      }
    }
    
    return $result;
  }
}