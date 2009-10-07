<?php
class Text_CsvReader_Grepper_Regex extends Text_CsvReader_Grepper
{
  protected $requiredOptions = array('pattern');
  protected $targetOptions = array('pattern');

  protected function accept($value, $column_index) {
    $pattern = $this->getOption('pattern');
    if (!empty($pattern[$column_index])) {
      return (boolean)preg_match($pattern[$column_index], $value);
    }
    return true;
  }
}
