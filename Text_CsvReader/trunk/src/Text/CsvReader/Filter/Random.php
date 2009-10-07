<?php
class Text_CsvReader_Filter_Random extends Text_CsvReader_Mapper
{
  protected $requiredOptions = array('min', 'max');
  protected $targetOptions = array('min', 'max');

  protected function map($value, $column_index)
  {
    return mt_rand($this->getOption('min', $column_index),
                   $this->getOption('max', $column_index));
  }
}
