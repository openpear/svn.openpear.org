<?php
class Text_CsvReader_Filter_Random extends Text_CsvReader_Mapper
{
  protected $options = array('range' => array(),
                             'choice' => array());
  protected $targetOptions = array('range', 'choice');
  protected $randomValues = array();

  public function rewind()
  {
    $this->fetchRandomValue();
    return parent::rewind();
  }
  public function next()
  {
    $this->fetchRandomValue();
    return parent::next();
  }
  protected function fetchRandomValue()
  {
    $column_indexes = $this->getTargetColumns();
    foreach ($column_indexes as $column_index) {
      if ($this->hasOption('choice', $column_index)) {
        $choice = $this->getOption('choice', $column_index);
        $value = $choice[mt_rand(0, sizeof($choice)-1)];
      } else {
        $range = $this->getOption('range', $column_index);
        $value = mt_rand($range[0], $range[1]);
      }
      $this->randomValues[$column_index] = $value;
    }
  }
  protected function map($value, $column_index)
  {
    return $this->randomValues[$column_index];
  }
}
