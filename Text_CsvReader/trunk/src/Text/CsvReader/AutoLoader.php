<?php
class Text_CsvReader_AutoLoader
{
  static protected $instance = null;
  protected $path = array();

  static public function getInstance()
  {
    if (!self::$instance) {
      self::$instance = new Text_CsvReader_AutoLoader();
    }
    return self::$instance;
  }
  public function __construct()
  {
    $files = glob(dirname(__FILE__)."/{,*/}*.php", GLOB_BRACE);
    foreach ($files as $file) {
      $class_name = preg_replace(array('|^.*/(Text/CsvReader(/\w+)?/\w+)\.php$|',
                                       '|/|',
                                       '/^Text_CsvReader_CsvReader/'),
                                 array('$1','_', 'CsvReader'),
                                 $file);
      $class_name = strtolower($class_name);
      $this->path[$class_name] = $file;
    }
  }
  public function autoload($class_name)
  {
    $class_name = strtolower($class_name);
    if (isset($this->path[$class_name])) {
      include($this->path[$class_name]);
    }
  }
}
spl_autoload_register(array(Text_CsvReader_Autoloader::getInstance(),'autoload'));
