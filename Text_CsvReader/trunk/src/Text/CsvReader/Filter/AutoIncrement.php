<?php
class Text_CsvReader_Filter_AutoIncrement extends Text_CsvReader_Mapper
{
  protected $requiredOptions = array('target');
  protected $counter = 0;

  public function rewind()
  {
    $this->counter = 1;
    return parent::rewind();
  }
  public function next()
  {
    $this->counter++;
    return parent::next();
  }

  protected function map($value, $column_index)
  {
    return $this->counter;
  }
}
