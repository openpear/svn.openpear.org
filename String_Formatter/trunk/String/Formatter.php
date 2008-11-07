<?php
/**
 * String_Formatter
 * Human like string format implementation
 *
 * @version: 0.1.0 <2008/10/26 16:49>
 * @package: String_Formatter
 * @author: Shuhei Tanuma <shuhei.tanuma at gmail.com>
 *
 * @example:
 * $fmt = new String_Formatter();
 * // Default left delimiter is "[:"
 * // Default right delimiter is ":]"
 * // Default internal delimiter is "\0". ������� "\0" �͓���Ȃ��łˁB
 * // You can change delimiter.
 * // $fmt->setConfig("ldelimiter","<?");
 * // $fmt->setConfig("rdelimiter","?>");
 * // $fmt->setConfig("delimiter",","); //�����̋�؂蕶�����ύX���邱�Ƃ��ł��܂��B
 *
 * $stmt = $fmt->prepare("[:target:] is very [:saywhat:]\n");
 * $stmt["target"] = "PHP";
 * $stmt["saywhat"] = "usefull!";
 * // ����ł�����
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
require_once dirname(__FILE__) . '/Formatter/Const.php';
require_once dirname(__FILE__) . '/Formatter/Core.php';
require_once dirname(__FILE__) . '/Formatter/Config.php';
require_once dirname(__FILE__) . '/Formatter/Statement.php';




/**
 * String_Formatter
 * String Formatter Main Class
 *
 * @version: 0.1.0 <2008/10/26 16:49>
 * @package: String_Formatter
 * @author: Shuhei Tanuma <shuhei.tanuma at gmail.com>
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
