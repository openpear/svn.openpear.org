<?php
class Text_CsvReader_Grepper_Unique extends Text_CsvReader_Grepper
{
  protected $requiredOptions = array('target');
  protected $already_exists = array();
  protected function acceptAll($values)
  {
    $indexes = $this->getTargetColumns($values);
    $key = null;
    foreach ($indexes as $index) {
      if (!isset($key)) {
        $key = $values[$index];
      } else {
        $key = $key."\0".$values[$index];
      }
    }
    if (isset($this->already_exists[$key])) {
      return false;
    }
    $this->already_exists[$key] = 1;
    return true;
  }
}
