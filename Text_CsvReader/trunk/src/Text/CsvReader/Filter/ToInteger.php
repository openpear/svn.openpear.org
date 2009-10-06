<?php
class Text_CsvReader_Filter_ToInteger extends Text_CsvReader_Mapper
{
  protected function map($value) {
    return (int)$value;
  }
}
