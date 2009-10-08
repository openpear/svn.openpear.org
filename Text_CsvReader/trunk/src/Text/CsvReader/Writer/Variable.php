<?php
class Text_CsvReader_Writer_Variable extends Text_CsvReader_Writer
{
  protected
    $requiredOptions = array('name'),
    $options = array('key' => null, 'value' => null),
    $values = array();
  public function initialize()
  {
    $this->values = array();
  }
  public function write($values) {
    if ($this->hasOption('key') && $this->hasOption('value')) {
      $key = $values[$this->getOption('key')];
      $value = $values[$this->getOption('value')];
      $this->values[$key] = $value;
    } else {
      $this->values[] = $values;
    }
  }
  public function finalize() {
    Text_CsvReader::setArray($this->getOption('name'), $this->values);
  }
  public function rollback() {
  }
}
