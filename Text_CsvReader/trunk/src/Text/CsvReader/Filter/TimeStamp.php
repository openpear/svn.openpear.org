<?php
class Text_CsvReader_Filter_TimeStamp extends Text_CsvReader_Mapper
{
  protected $requiredOptions = array('target');

  protected function map($value, $column_index)
  {
    return date("Y-m-d H:i:s", $value);
  }
}
