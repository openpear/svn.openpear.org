<?php
class Text_CsvReader_Writer_Variable extends Text_CsvReader_Writer
{
  protected
    $requiredOptions = array('name'),
    $options = array(),
    $values = array();
  public function initialize()
  {
    $this->values = array();
  }
  public function write($values) {
    $this->values[] = $values;
  }
  public function finalize() {
    Text_CsvReader::setVariable($this->getOption('name'), $this->values);
  }
  public function rollback() {
  }
}
