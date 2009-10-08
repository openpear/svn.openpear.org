<?php
class Text_CsvReader_Filter_Variable extends Text_CsvReader_Mapper
{
  protected $requiredOptions = array('name');
  protected $targetOptions = array('name');

  protected function map($value, $column_index)
  {
    $name = $this->getOption('name', $column_index);
    return Text_CsvReader::getArrayValue($name, $value);
  }
}
