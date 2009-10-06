<?php
class Text_CsvReader_Filter_StrReplace extends Text_CsvReader_Mapper
{
  protected $requiredOptions = array('from', 'to');
  protected $targetOptions = array('from', 'to');

  protected function map($value, $column_index)
  {
    return str_replace($this->getOption('from', $column_index),
                       $this->getOption('to', $column_index),
                       $value);
  }
}
