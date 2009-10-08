<?php
class Text_CsvReader_Filter_AutoIncrement extends Text_CsvReader_Mapper
{
  // target に統一したいなー。
  protected $requiredOptions = array('column');
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

  protected function mapAll($values)
  {
    $values[$this->getOption('column')] = $this->counter;
    return $values;
  }
}
