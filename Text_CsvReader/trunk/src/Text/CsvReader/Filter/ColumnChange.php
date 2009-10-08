<?php
class Text_CsvReader_Filter_ColumnChange extends Text_CsvReader_Filter
{
  protected $options = array('column' => array(),
                             'max_column' => 0);
  public function current() {
    $values = parent::current();
    if (!is_array($this->getOption('column'))) {
      throw new CsvReaderException('column option must be an array');
    }
    $new_values = array();
    $max_index = -1;
    if ($this->hasOption('max_column')) {
      $max_index = $this->getOption('max_column');
    }
    $to_from_indexes = $this->getOption('column');
    foreach ($to_from_indexes as $to_index => $from_index) {
      if ($max_index < $to_index) {
        $max_index = $to_index;
      }
    }
    for ($i=0; $i <= $max_index; $i++) {
      $new_values[$i] = null;
    }
    foreach ($to_from_indexes as $to_index => $from_index) {
      $new_values[$to_index] = $values[$from_index];
    }
    return $new_values;
  }
}
