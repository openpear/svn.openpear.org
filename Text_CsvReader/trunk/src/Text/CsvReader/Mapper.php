<?php
abstract class Text_CsvReader_Mapper extends Text_CsvReader_Filter
{
  public function __construct(Iterator $iterator, $options = array(), $messages = array())
  {
    parent::__construct($iterator, $options, $messages);
  }
  public function current()
  {
    return $this->mapAll(parent::current());
  }
  protected function mapAll($values)
  {
    $column_indexes = $this->getTargetColumns($values);
    foreach ($column_indexes as $column_index) {
      $values[$column_index] = $this->map($values[$column_index], $column_index);
    }
    return $values;
  }
  protected function map($value, $column_index)
  {
    return $value;
  }
}
