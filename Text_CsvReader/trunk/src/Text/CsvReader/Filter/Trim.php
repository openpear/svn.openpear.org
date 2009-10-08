<?php
class Text_CsvReader_Filter_Trim extends Text_CsvReader_Mapper
{
  protected $options = array('target' => null);

  protected function map($value)
  {
    return trim($value);
  }
}
