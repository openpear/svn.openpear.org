<?php
class Text_CsvReader_Filter_AutoIncrement extends Text_CsvReader_Mapper
{
  protected $requiredOptions = array('column');
  protected $counter = 0;

  protected function initialize()
  {
    $this->counter = 0;
  }

  protected function mapAll($values)
  {
    $values[$this->getOption('column')] = ++$this->counter;
    return $values;
  }
}
