<?php
class Text_CsvReader_Filter_PregReplace extends Text_CsvReader_Mapper
{
  protected $requiredOptions = array('pattern', 'replacement');
  protected $targetOptions = array('pattern', 'replacement');

  protected function map($value, $column_index)
  {
    return preg_replace($this->getOption('pattern', $column_index),
                        $this->getOption('replacement', $column_index),
                        $value);
  }
}
