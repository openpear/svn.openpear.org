<?php
class Text_CsvReader_Validator_String extends Text_CsvReader_Validator
{
  protected $targetOptions = array('min_length', 'max_length');
  protected $options = array('min_length' => array(),
                             'max_length' => array());

  protected function validate($value, $column_index) {
    $length = strlen($value);
    if ($this->hasOption('max_length', $column_index)
        && $length > $this->getOption('max_length', $column_index)) {
      throw new CsvReaderValidatorException(sprintf("カラム最大長%dを超えています: %d",
                                                    $this->getOption('max_length', $column_index),
                                                    $length));
    }
    if ($this->hasOption('min_length', $column_index)
        && $length < $this->getOption('min_length', $column_index)) {
      throw new CsvReaderValidatorException(sprintf("カラム最短長%dより短いです: %d",
                                                    $this->getOption('min_length', $column_index),
                                                    $length));
    }
  }
}
