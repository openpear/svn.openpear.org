<?php
class Text_CsvReader_Filter_Constant extends Text_CsvReader_Mapper
{
  protected $requiredOptions = array('value');
  protected $targetOptions = array('value');

  protected function map($value, $column_index)
  {
    return $this->getOption('value', $column_index);
  }
}
