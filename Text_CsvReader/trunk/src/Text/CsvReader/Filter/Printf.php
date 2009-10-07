<?php
class Text_CsvReader_Filter_Printf extends Text_CsvReader_Mapper
{
  protected $requiredOptions = array('format');
  protected $targetOptions = array('format');

  protected function map($value, $column_index)
  {
    return sprintf($this->getOption('format', $column_index),
                   $value);
  }
}
