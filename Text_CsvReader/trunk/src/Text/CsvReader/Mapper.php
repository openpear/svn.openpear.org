<?php
abstract class Text_CsvReader_Mapper extends Text_CsvReader_Filter
{
  public function __construct(Iterator $iterator, $options = array(), $messages = array()) {
    parent::__construct($iterator, $options, $messages);
  }
  public function current() {
    return $this->mapAll(parent::current());
  }
  protected function mapAll($values) {
    if ($this->hasOption('target') && is_array($this->getOption('target'))) {
      $indexes = $this->getOption('target');
    } else {
      $indexes = array_keys($values);
    }
    foreach ($indexes as $index) {
      $values[$index] = $this->map($values[$index]);
    }
    return $values;
  }
  protected function map($value) {
    return $value;
  }
}
