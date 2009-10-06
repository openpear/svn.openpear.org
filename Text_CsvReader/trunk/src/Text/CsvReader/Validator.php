<?php
abstract class Text_CsvReader_Validator extends Text_CsvReader_Base
{
  protected $cached = false;
  protected $currentValue = null;

  public function validateAll($values) {
    $column_indexes = $this->getTargetIndexes($values);
    $caughtException = null;
    foreach ($column_indexes as $column_index) {
      try {
        $this->validate($values[$column_index], $column_index);
      } catch (CsvReaderValidatorException $e) {
        if ($caughtException) {
          $caughtException->addErrors($e);
        } else {
          $caughtException = $e;
        }
      }
    }
    if ($caughtException) {
      throw $caughtException;
    }
  }
  protected function validate() {
  }
}