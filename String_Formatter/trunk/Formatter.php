<?php
/**
 * String_Formatter
 * Human like string format implementation
 *
 * @version: 0.1.0 <2008/10/26 16:49>
 * @package: String_Formatter
 * @author: Shuhei Tanuma <shuhei.tanuma@gmail.com>
 *
 * @example:
 * $fmt = new String_Formatter();
 * // Default left delimiter is "[:"
 * // Default right delimiter is ":]"
 * // Default internal delimiter is "\0". •¶Žš—ñ‚É "\0" ‚Í“ü‚ê‚È‚¢‚Å‚ËB
 * // You can change delimiter.
 * // $fmt->setConfig("ldelimiter","<?");
 * // $fmt->setConfig("rdelimiter","?>");
 * // $fmt->setConfig("delimiter",","); //“à•”‚Ì‹æØ‚è•¶Žš—ñ‚ð•ÏX‚·‚é‚±‚Æ‚à‚Å‚«‚Ü‚·B
 *
 * $stmt = $fmt->prepare("[:target:] is very [:saywhat:]\n");
 * $stmt["target"] = "PHP";
 * $stmt["saywhat"] = "usefull!";
 * // ‚±‚ê‚Å‚à‚¨‚‹
 * // $stmt->assign("target","PHP");
 * // $stmt->assign("saywhat","usefull!");
 *
 * echo $stmt->toString();
 *   // Output:PHP is very usefull!
 *
 * @Inherits
 *  String_Formatter_Core
 *   `String_Formatter
 *   `String_Formatter_Config
 *   `String_Formatter_Statement implements ArrayAccess
 *  String_Formatter_Const
 *
**/



/**
 * String_Formatter_Core
 * String Formatter Core Class
 *
 * @version: 0.1.0 <2008/10/26 16:49>
 * @package: String_Formatter
 * @author: Shuhei Tanuma <shuhei.tanuma@gmail.com>
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


/**
 * String_Formatter_Statement
 * String Formatter Statement Class
 *
 * @version: 0.1.0 <2008/10/26 16:49>
 * @package: String_Formatter
 * @author: Shuhei Tanuma <shuhei.tanuma@gmail.com>
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

/**
 * String_Formatter_Config
 * String Formatter Config Class
 *
 * @version: 0.1.0 <2008/10/26 16:49>
 * @package: String_Formatter
 * @author: Shuhei Tanuma <shuhei.tanuma@gmail.com>
 *
**/
final class String_Formatter_Config extends String_Formatter_Core{
  protected $ldelimiter;
  protected $rdelimiter;
  protected $delimiter;
  protected $regexdelimiter;

  protected function __construct(){
    $this->delimiter =  String_Formatter_Const::DefaultDelimiter;
    $this->ldelimiter = String_Formatter_Const::DefaultLeftDelimiter;
    $this->rdelimiter = String_Formatter_Const::DefaultRightDelimiter;
    $this->regexdelimiter = String_Formatter_Const::DefaultRegexDelimiter;
    $this->regexp = String_Formatter_Const::DefaultRegexp;
  }
}

final class String_Formatter_Const{
  const DefaultDelimiter = "\0";
  const DefaultLeftDelimiter = "[:";
  const DefaultRightDelimiter = ":]";
  const DefaultRegexDelimiter = "/";
  const DefaultRegexp = "\s*[a-zA-Z0-9][a-zA-Z0-9_-]*?\s*";
}

/**
 * String_Formatter
 * String Formatter Main Class
 *
 * @version: 0.1.0 <2008/10/26 16:49>
 * @package: String_Formatter
 * @author: Shuhei Tanuma <shuhei.tanuma@gmail.com>
 *
**/
class String_Formatter extends String_Formatter_Core{

  public function __construct($string = null){
    $this->statement = $string;
    $this->config = parent::InitializeConfig();
  }

  public function prepare(){
    $numargs = func_num_args();
    $args = func_get_args();
    if($numargs){
      $this->statement = join("",$args);
    }

    return parent::createStatement($this->statement,$this->config);
  }
}
