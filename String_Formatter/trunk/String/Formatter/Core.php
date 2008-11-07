<?php
/**
 * String_Formatter_Core
 * String Formatter Core Class
 *
 * @version: 0.1.0 <2008/10/26 16:49>
 * @package: String_Formatter
 * @author: Shuhei Tanuma <shuhei.tanuma at gmail.com>
 *
**/
class String_Formatter_Core{
  protected $config;

  protected function createStatement($stmt,$config){
    return String_Formatter_Statement::Initialize($this->statement,$config);
  }
  
  protected function InitializeConfig(){
    return new String_Formatter_Config();
  }
  
  public function getConfig($offset){
    return $this->config->$offset;
  }
  
  public function setConfig($offset,$value){
    switch($offset){
      case "delimiter":
      case "ldelimiter":
      case "rdelimiter":
        $this->config->$offset = $value;
        break;
      default:
    }
  }
}