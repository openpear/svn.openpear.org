<?php
class Text_CsvReader_Grepper_Unique extends Text_CsvReader_Grepper
{
  protected $already_exists = array();
  protected function acceptAll($values) {
    $indexes = $this->getTargetColumns($values);
    $key = "";
    foreach ($indexes as $index) {
      $key .= $values[$index];
    }
    if (isset($this->already_exists[$key])) {
      return false;
    }
    $this->already_exists[$key] = 1;
    return true;
  }
}
