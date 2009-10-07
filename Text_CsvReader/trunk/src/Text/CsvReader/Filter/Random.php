<?php
class Text_CsvReader_Filter_Random extends Text_CsvReader_Mapper
{
  protected $options = array('min' => array(),
                             'max' => array(),
                             'choice' => array());
  protected $targetOptions = array('min', 'max', 'choice');

  protected function map($value, $column_index)
  {
    if ($this->hasOption('choice', $column_index)) {
      $choice = $this->getOption('choice', $column_index);
      return $choice[mt_rand(0, sizeof($choice)-1)];
    } else {
      return mt_rand($this->getOption('min', $column_index),
                     $this->getOption('max', $column_index));
    }
  }
}
