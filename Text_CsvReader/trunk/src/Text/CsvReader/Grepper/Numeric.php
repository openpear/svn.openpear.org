<?php
class Text_CsvReader_Grepper_Numeric extends Text_CsvReader_Grepper
{
  protected function accept($value) {
    return is_numeric($value);
  }
}
