<?php
/**
 * String_Formatter_Statement
 * String Formatter Statement Class
 *
 * @version: 0.1.0 <2008/10/26 16:49>
 * @package: String_Formatter
 * @author: Shuhei Tanuma <shuhei.tanuma at gmail.com>
 *
**/
final class String_Formatter_Statement extends String_Formatter_Core implements ArrayAccess{
  private $statement;
  private $placeHolder;
  private $joint;
  private $values;
  private $references;

  private function __construct($statement,$config){
    $this->placeHolder = array();
    $this->references  = array();
    $this->joint       = array();
    $this->values      = array();
    $this->config      = $config;
    $this->statement   = $statement;

    $this->parseStatement();
  }

  
  protected static function Initialize($statement,$config){
    $stmt = new String_Formatter_Statement($statement,$config);

    return $stmt;
  }

  protected function split($string){
    $c = strlen($string);
    $this->joint = array();
    $tmp = "";

    $r = 0;
    for($i=0;$i<$c;$i++){
      if($string[$i] == $this->getConfig("delimiter")){
        if(strlen($tmp) > 0){
          $this->joint[] = $tmp;
          $this->references[$this->placeHolder[$r]][] = count($this->joint);
          $this->joint[] = &$this->values[$this->placeHolder[$r]];
          $tmp = "";

          $r++;
        }else{
          $this->references[$this->placeHolder[$r]][] = count($this->joint);
          $this->joint[] = &$this->values[$this->placeHolder[$r]];

          $r++;
        }

      }else{
        $tmp .= $string[$i];
      }
    }
    if(strlen($tmp)){
      $this->joint[] = $tmp;
    }
  }

  protected function callback($match){
    $key = $match[0];
    $k = str_replace(
      array(
        $this->getConfig("ldelimiter"),
        $this->getConfig("rdelimiter")
      ),
      array('',''),
      $key
    );

    $this->placeHolder[] = trim($k);
    return $this->getConfig("delimiter");
  }

  protected function parseStatement(){
    $result = preg_replace_callback(
      sprintf("%s%s%s%s%s",
        $this->getConfig("regexdelimiter"),
        preg_quote($this->getConfig("ldelimiter"),$this->getConfig("regexdelimiter")),
        $this->getConfig("regexp"),
        preg_quote($this->getConfig("rdelimiter"),$this->getConfig("regexdelimiter")),
        $this->getConfig("regexdelimiter")
      ),
      array(get_class($this),"callback")
      ,$this->statement);

    $this->split($result);
  }
  
  public function __toString(){
    return $this->toString();
  }
  
  public function toString(){
    return join("",$this->joint);
  }
  
  public function assign_by_ref($offset, &$value){
    foreach($this->references[$offset] as $count){
      $this->joint[$count] = &$value;
    }
  }
  
  public function assign($offset,$value,$opt="%s"){
    $this->offsetSet($offset,sprintf($opt,$value));
  }

  /**
  * Below method are SPL Implementation.
  **/
  public function offsetGet($offset){
    return $this->values[$offset];
  }

  public function offsetSet($offset, $value){
    $this->values[$offset] = $value;

  }

  public function offsetExists($offset){
    return isset($this->values[$offset]);
  }

  public function offsetUnset($offset){
    unset($this->values[$offset]);
  }
}