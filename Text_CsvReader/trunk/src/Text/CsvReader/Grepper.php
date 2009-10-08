<?php
abstract class Text_CsvReader_Grepper extends Text_CsvReader_Filter
{
  public function __construct(Iterator $iterator, $options = array(), $messages = array()) {
    parent::__construct($iterator, $options, $messages);
  }
  public function rewind() {
    $ret = $this->getInnerIterator()->rewind();
    $this->fetch();
    return $ret;
  }
  public function next() {
    $ret = $this->getInnerIterator()->next();
    $this->fetch();
    return $ret;
  }
  protected function fetch() {
    while ($this->valid()) {
      if ($this->acceptAll($this->current())) {
        return;
      }
      $this->getInnerIterator()->next();
    }
  }

  protected function acceptAll($values) {
    $column_indexes = $this->getTargetColumns($values);
    foreach ($column_indexes as $column_index) {
      if ($this->accept($values[$column_index], $column_index) !== true) {
        return false;
      }
    }
    return true;
  }
  protected function accept($value, $column_index) {
    return true;
  }
}