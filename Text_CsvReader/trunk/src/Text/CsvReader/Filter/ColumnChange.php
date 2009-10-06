<?php
class Text_CsvReader_Filter_ColumnChange extends Text_CsvReader_Filter
{
  protected $requiredOptions = array('column');
  public function current() {
    $values = parent::current();
    if (!is_array($this->getOption('column'))) {
      throw new CsvReaderException('column option must be an array');
    }
    $new_values = array();
    $max_index = -1;
    $to_column_index = $this->getOption('column');
    foreach ($to_column_index as $from_index => $to_index) {
      if ($max_index < $to_index) {
        $max_index = $to_index;
      }
    }
    for ($i=0; $i <= $max_index; $i++) {
      $new_values[$i] = null;
    }
    foreach ($this->getOption('column') as $from_index => $to_index) {
      $new_values[$to_index] = $values[$from_index];
    }
    return $new_values;
  }
}
