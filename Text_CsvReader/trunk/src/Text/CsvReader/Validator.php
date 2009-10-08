<?php
abstract class Text_CsvReader_Validator extends Text_CsvReader_Base
{
  protected $cached = false;
  protected $currentValue = null;

  public function validateAll($values)
  {
    $column_indexes = $this->getTargetColumns($values);
    $caughtErrors = array();
    foreach ($column_indexes as $column_index) {
      $errors = $this->validate($values[$column_index], $column_index);
      if ($errors) {
        if (!is_array($errors)) {
          $errors = array($errors);
        }
        $caughtErrors[$column_index] = $errors;
      }
    }
    return $caughtErrors;
  }
  protected function validate() {
  }
}