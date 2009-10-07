<?php
class Text_CsvReader_Filter_Random extends Text_CsvReader_Mapper
{
  protected $options = array('range' => array(),
                             'choice' => array());
  protected $targetOptions = array('range', 'choice');

  protected function map($value, $column_index)
  {
    if ($this->hasOption('choice', $column_index)) {
      $choice = $this->getOption('choice', $column_index);
      return $choice[mt_rand(0, sizeof($choice)-1)];
    } else {
      $range = $this->getOption('range', $column_index);
      return mt_rand($range[0], $range[1]);
    }
  }
}
