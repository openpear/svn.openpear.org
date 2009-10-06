<?php

/**
 * CsvReader: 拡張性の高いCSV読み込みクラス
 *
 * @author  HANAWA Yoshio
 */

include(dirname(__FILE__).'/CsvReader/AutoLoader.php');

class Text_CsvReader
{
  protected
    $sheets = array();
  protected static
    $variable = array();

  public function configure($whole_options = array())
  {
    foreach($whole_options as $sheet_name => $options) {
      $this->sheets[$sheet_name] = new Text_CsvReader_Sheet($options);
    }
  }
  protected function normalizeTargetSheets($target_sheets)
  {
    if ($target_sheets === null) {
      $target_sheets = array_keys($this->sheets);
    }
    if (!is_array($target_sheets)) {
      $target_sheets = array($target_sheets);
    }
    return $target_sheets;
  }

  public function process($target_sheets = null, $enable_writers = true)
  {
    $target_sheets = $this->normalizeTargetSheets($target_sheets);
    $ret = true;
    foreach ($target_sheets as $sheet_name) {
      $ret = $ret && $this->sheets[$sheet_name]->processSheet($enable_writers);
    }
    return $ret;
  }
  public function getErrors($sheet_name)
  {
    if (isset($this->sheets[$sheet_name])) {
      return $this->sheets[$sheet_name]->getErrors();
    }
    return array();
  }
  public function showErrors($target_sheets = null)
  {
    $target_sheets = $this->normalizeTargetSheets($target_sheets);
    foreach ($target_sheets as $sheet_name) {
      $errors = $this->getErrors($sheet_name);
      foreach ($errors as $error) {
        printf("%s\n", $error);
      }
    }
  }
  static public function getVariable($name)
  {
    return isset(self::$variable[$name]) ? self::$variable[$name] : null;
  }
  static public function setVariable($name, $value)
  {
    self::$variable[$name] = $value;
  }
}
