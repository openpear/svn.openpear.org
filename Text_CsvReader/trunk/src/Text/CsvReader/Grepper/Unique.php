<?php
class Text_CsvReader_Grepper_Unique extends Text_CsvReader_Grepper
{
  protected $already_exists = array();
  protected function acceptAll($values) {
    if ($this->hasOption('target') && is_array($this->getOption('target'))) {
      $indexes = $this->getOption('target');
    } else {
      $indexes = array_keys($values);
    }
    $key = "";
    $values = $this->current();
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
