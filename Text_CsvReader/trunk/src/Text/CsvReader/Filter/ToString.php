<?php
class Text_CsvReader_Filter_ToString extends Text_CsvReader_Mapper
{
  protected function map($value) {
    return (string)$value;
  }
}
